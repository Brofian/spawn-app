import EventManager from "../../../../../../../../src/npm/plugin-system/EventManager";

export default class AjaxDataReplace {


    static loadAndReplaceContent(contentUrl, targetSelector) {

        let targetElement = document.querySelector(targetSelector);
        if(!targetElement) {
            return;
        }
        this.createLoadingSpinner(targetElement);

        //load url content
        $.ajax(
            {
                url: contentUrl
            }
        ).done(
            this.replaceContent.bind(null, targetSelector, targetElement)
        ).fail(
            this.replaceContentWithError.bind(null, targetElement)
        );



    }

    static replaceContentWithError(targetElement) {
        let errorElement = document.createElement('div');
        errorElement.innerText = 'ERROR';
        targetElement.parentNode.replaceChild(errorElement, targetElement);
    }

    static replaceContent(targetSelector, targetElement, requestData, resultCode, response) {

        let container = document.createElement('template');
        container.innerHTML = requestData.trim();
        let urlContentElement = container.content.querySelector(targetSelector);


        if(!urlContentElement) {
            urlContentElement = container.content;
        }

        targetElement.parentNode.replaceChild(urlContentElement, targetElement);

        EventManager.publish('pluginmanager.startInitializeScope', [urlContentElement]);
    }

    static createLoadingSpinner(targetElement) {

        let backdrop = document.createElement('div');
        backdrop.classList.add('loading-backdrop');

        let spinner = document.createElement('div');
        spinner.classList.add('loading-spinner');
        backdrop.appendChild(spinner);

        targetElement.appendChild(backdrop);
    }




}