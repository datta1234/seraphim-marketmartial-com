export default class Message {

    constructor(options) {

        this._timeout = null;
        this.packets: [];
        this.data:[];

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

    addChunkData(packet) {
        // Check if we already have the packet if not -
        // Add packet number to this.packets in order
        // Add b64 data to this.data in same place as packet number above
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