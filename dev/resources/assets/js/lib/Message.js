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

    addChunkData(chunk_data) {
        // Check if we already have the packet if not -
        let index = this.packets.findIndex( (packet) => {
            return packet == chunk_data.packet;
        });

        // Add packet number to this.packets
        // Add b64 data to this.data
        if(index === -1) {
            this.packets.push(chunk_data.packet);
            this.data.push(chunk_data.data);
        }
        
    }

    requestMissingChunks() {
        // make axios call for a list of missing chunk data
        // success - add new chunk data
        // fail - expire remove message instance
    }

    getUnpackedData() {
        // Concat this.data into single string.
        // Decode b64 string
        // Parse to object and return object
    }

    setTimeout() {
        // set the timeout ref for this packet
    }
}