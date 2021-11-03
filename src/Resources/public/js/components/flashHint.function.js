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
        flashHint.innerText = text;

        //remove flashAfterLifetime
        window.setTimeout((function (flashHint) {
            flashHint.parentElement.removeChild(flashHint);
        }).bind(null, flashHint), 5000);



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