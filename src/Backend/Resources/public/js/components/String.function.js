export default class String {

    static trim(value, charlist = ' \n\r\t') {
        //value = String.trimRight(value, charlist);
        //value = String.trimLeft(value, charlist);

        return value;
    }

    static trimLeft(value, charlist = ' \n\r\t') {

        let hasRemovedChar = false;
        do {
            hasRemovedChar = false;
            for(let charToRemove of charlist) {
                if(value.charAt(0) === charToRemove) {
                    value = value.substring(1);
                    hasRemovedChar = true;
                    break;
                }
            }
        }
        while(hasRemovedChar);

        return value;
    }

    static trimRight(value, charlist = ' \n\r\t') {

        let hasRemovedChar = false;
        do {
            hasRemovedChar = false;
            for(let charToRemove of charlist) {
                if(value.charAt(value.length-1) === charToRemove) {
                    value = value.substring(0,value.length-1);
                    hasRemovedChar = true;
                    break;
                }
            }
        }
        while(hasRemovedChar);

        return value;
    }




}