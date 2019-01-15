/*
*   Resolve the sluggified trade_structure from a string
*/
exports.resolveTradeStructureSlug = (string_title) => {
    switch(string_title) {
        case 'Outright':
            return 'outright';
        break;
        case 'Risky':
            return 'risky';
        break;
        case 'Calendar':
            return 'calendar';
        break;
        case 'Fly':
            return 'fly';
        break;
        case 'Option Switch':
            return 'option_switch';
        break;
        case 'EFP Switch':
            return 'efp_switch';
        break;
        case 'EFP':
            return 'efp';
        break;
        case 'Rolls':
            return 'rolls';
        break;
        case 'Var Swap':
            return 'var_swap';
        break;
    };
    return null;
}