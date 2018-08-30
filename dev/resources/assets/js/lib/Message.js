const message_timeout = 5000;
const missing_packet_path = '/trade/';
import Sha256 from './Char256Hash/sha256';
import crypto from 'crypto';
export default class Message {

    /**
     * Constructs a new Message instance using defaults as the params that can be passed
     */
    constructor(options, callback) {

        this._timeout = null;
        this.packets = [];
        this.missing_packets = [];
        this.data = [];
        this.can_request_missing = true;
        this.timestamp = null;
        this.callback = callback;

        const defaults = {
            checksum: '',
            total: 0,
            expires: moment(),
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
                if(defaults[key] instanceof moment) {
                    this[key] = moment(this[key]);
                }
            } else {
                this[key] = defaults[key];
            }
        });

        if(options && options.total) {
            this.generateMissingPackets();
        }
    }

    get full_message() {
        // Sort packets to their order
        this.sortPackets();
        // Concat this.data into single string.
        return this.data.reduce( (accumulator, currentValue) => {
            return accumulator + currentValue;
        }, '');
    }

    /**
     * Generates a new list of missing packets using the total number of packets
     */
    generateMissingPackets() {
        for(let i = 1; i <= this.total; i++) {
            this.missing_packets.push(i);
        }
    }

    /**
     * Adds a new chunk packet to the message
     * 
     * @param {Object} chunk - a new chunk packet.
     */
    addChunk(chunk) {
        // Check if we already have the packet if not -
        let index = this.packets.findIndex( (packet) => {
            return packet == chunk.packet;
        });

        // Add packet number to this.packets
        // Add b64 data to this.data
        if(index === -1) {
            let missing_index = this.missing_packets.findIndex( (missing_packet) => {
                return missing_packet == chunk.packet;
            });
            this.missing_packets.splice(missing_index, 1);
            this.packets.push(chunk.packet);
            this.data.push(chunk.data);
            this.timestamp = moment(chunk.timestamp);
        }
        // clear current timeouts
        clearTimeout(this._timeout);
        // creates new timeout
        if(this.packets.length !== this.total) {
            this._timeout = setTimeout(this.requestMissingChunks, message_timeout);
        }
    }

    /**
     * Adds an Array of chunk packets
     * 
     * @param {Object[]} chunks - an array of new chunk packets
     */
    addChunks(chunks) {
        chunks.forEach(chunk => {
            this.addChunk(chunk);
        });
    }

    /**
     * Makes an axios post request to get missing chunks 
     */
    requestMissingChunks() {
        return axios.post(axios.defaults.baseUrl + missing_packet_path, {
            "checksum": this.checksum,
            "missing_packets":this.missing_packets
        })
        .then(missingChunkDataResponse => {
            // success - add new chunk data
            switch(missingChunkDataResponse.status) {
                case 200:
                    this.addChunks(missingChunkDataResponse.data.data);
                break;
                case 404: // fail - expire remove message instance
                    this.can_request_missing = false;
                break;
                default:
                    console.error(err);
            }
            return missingChunkDataResponse;
        })
        .catch(err => {
            console.error(err);
        });
    }

    /**
     * Unpacks completed base64 message data and converts in to a JS object
     * 
     * @return {Object} Object of the converted base 64 data string
     */
    getUnpackedData() {
        if(this.packets.length !== this.total) {
            return null;
        }

        let base64_string = this.full_message;

        // Decode b64 string and Parse to object and return object
        if( this.validateChecksum(base64_string) ) {
            this.can_request_missing = false;
            try {
                return JSON.parse(atob(base64_string));
            } catch(err) {
                console.error(err);
                return null;
            }
        } else {
            this.missing_packets.splice(0);
            this.packets.splice(0);
            this.data.splice(0);
            this.generateMissingPackets();
            this.requestMissingChunks();
            return null;
        }
    }

    /**
    * Test if message is complete
    *
    * @return {Boolean}
    */
    isComplete() {
        return this.packets.length === this.total;
    }

    /**
    * Attempts to Complete message and run callback
    *
    * @return {Boolean}
    */
    doCompletion() {
        return new Promise((resolve, reject) => {
            // fail imediately if not complete
            if(this.packets.length !== this.total) {
                reject(false);
                return;
            }

            // handle corrupt message
            if( this.validateChecksum(this.full_message) ) {
                try {
                    if(typeof this.callback == 'function') {
                        this.callback(JSON.parse(atob(this.full_message)));
                    }
                    resolve(JSON.parse(atob(this.full_message)));
                } catch(err) {
                    reject(err);
                    return;
                }
            } else {
                this.missing_packets.splice(0);
                this.packets.splice(0);
                this.data.splice(0);
                this.generateMissingPackets();

                this.requestMissingChunks()
                .then(data => {
                    try {
                        if(typeof this.callback == 'function') {
                            this.callback(JSON.parse(atob(this.full_message)));
                        }
                        resolve(JSON.parse(atob(this.full_message)));
                    } catch(err) {
                        reject(err);
                        return;
                    }
                })
                .catch(reject);
            }
            return;
        });
    }

    /**
     * Basic bubble sort that sorts this.packets and this.data into numerical order using the
     *  packets index as reference for both the packets and the data.
     */
    sortPackets() {
        for(let i = 0; i < this.total - 1; i++) {
            for(let j = 0; j < this.total - i - 1; j++) {
                if( this.packets[j] > this.packets[j+1] ) {
                    let temp_packet = this.packets[j];
                    let temp_data = this.data[j];

                    this.packets[j] = this.packets[j+1];
                    this.data[j] = this.data[j+1];
                    
                    this.packets[j+1] = temp_packet;
                    this.data[j+1] = temp_data;
                }
            }
        }
    }

    /**
     * a Validation method that calculates the base 64 checksum of the passed string
     *  and evaluates it agains this.checksum
     * 
     * @param {String} base64_string - a base 64 string
     *
     * @return {Boolean}
     */
    validateChecksum(base64_string) {
        let calculated_checksum = new Sha256().update(base64_string, 'ascii').digest('hex');
        return this.checksum == calculated_checksum;
    }
}