export default class LoadingSpinner {

    /**
     * Creates the spinner animation for the given element
     * @param targetElement
     */
    static createLoadingSpinner(targetElement) {

        let backdrop = document.createElement('div');
        backdrop.classList.add('loading-backdrop');

        let targetHeight = Math.max(targetElement.clientHeight, targetElement.scrollHeight);
        backdrop.style.minHeight = targetHeight + 'px';

        let spinner = document.createElement('div');
        spinner.classList.add('loading-spinner');
        backdrop.appendChild(spinner);

        targetElement.appendChild(backdrop);
    }


    /**
     * Removes every Spinner element and its backdrop from the given element
     * @param targetElement
     */
    static removeLoadingSpinner(targetElement) {
        let spinners = document.getElementsByClassName('loading-backdrop');
        for (let spinner of spinners) {
            spinner.parentNode.removeChild(spinner);
        }
    }


}