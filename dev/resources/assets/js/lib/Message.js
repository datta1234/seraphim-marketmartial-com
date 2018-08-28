const message_timeout = 5000;
export default class Message {

    constructor(options) {

        this._timeout = null;
        this.packets = [];
        this.data = [];

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
    }

    addChunk(chunk) {
        // Check if we already have the packet if not -
        let index = this.packets.findIndex( (packet) => {
            return packet == chunk.packet;
        });

        // Add packet number to this.packets
        // Add b64 data to this.data
        if(index === -1) {
            this.packets.push(chunk.packet);
            this.data.push(chunk.data);
        }
        // clear current timeouts
        clearTimeout(this._timeout);
        // creates new timeout
        if(this.packets.length !== this.total) {
            this._timeout = setTimeout(this.requestMissingChunks, message_timeout);
        }
    }

    addChunks(chunks) {
        chunks.forEach(chunk => {
            this.addChunk(chunk);
        });
    }

    requestMissingChunks() {
        // make axios call for a list of missing chunk data
        // @TODO - add url for request
        return axios.get(axios.defaults.baseUrl + '/trade/')
            .then(missingChunkDataResponse => {
                // success - add new chunk data
                if(missingChunkDataResponse.status == 200) {
                    this.addChunks(missingChunkDataResponse.data.data);               
                // @TODO - Change status code to status sent as a result of expiry
                // fail - expire remove message instance
                } else if(missingChunkDataResponse.status == 200) {
                // @TODO - add any other data we might want to send back
                    return null;
                } else {
                    console.error(err);    
                }
            }, err => {
                console.error(err);
            });
    }

    getUnpackedData() {
        if(this.packets.length !== this.total) {
            return null;
        }
        // Sort packets to their order
        this.sortPackets();
        // Concat this.data into single string.
        let base64_string = this.data.reduce( (accumulator, currentValue) => {
            return accumulator + currentValue;
        }, '');
        // Decode b64 string and Parse to object and return object
        // @TODO - Add error handeling here
        return JSON.parse(atob(base64_string));
    }

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
}