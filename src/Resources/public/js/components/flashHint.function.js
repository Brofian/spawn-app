export default class FlashHint {

    /**
     * Creates a flash hint and its container
     * @param text
     * @param type
     */
    static createFlashHint(text, type = 'info') {

        //create Hint
        let flashHint = document.createElement('div');
        flashHint.classList.add('flash-hint');
        flashHint.classList.add(type + '-flash');
        let message = text.substring(0, 120);
        if(text.length > 120) {
            message += '...';
        }
        flashHint.innerHTML = '<span class="flash-icon icon-'+type+'"></span>' + message;


        //remove flashAfterLifetime (plus fadeout time of 25%)
        window.setTimeout((function (flashHint) {
            flashHint.parentElement.removeChild(flashHint);
        }).bind(null, flashHint), 5000*1.25);

        //search or create flash Container and append new item
        let flashContainer = document.getElementById('flash-hint-container');
        if(!flashContainer) {
            flashContainer = document.createElement('div');
            flashContainer.id = 'flash-hint-container';
            document.body.appendChild(flashContainer);
        }
        flashContainer.appendChild(flashHint);
    }

}