import EventManager from "EventManager";
import LoadingSpinner from "./loadingSpinner.function";

export default class AjaxDataReplace {

    static loadAndReplaceContent(contentUrl, targetSelector) {

        let targetElement = document.querySelector(targetSelector);
        if(!targetElement) {
            return;
        }
        LoadingSpinner.createLoadingSpinner(targetElement);

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

    /**
     * Replace the given elements content with a simple error message
     * @param targetElement
     */
    static replaceContentWithError(targetElement) {
        let errorElement = document.createElement('div');
        errorElement.innerText = 'ERROR';
        targetElement.parentNode.replaceChild(errorElement, targetElement);
    }

    /**
     * Replace the current content of the new content and initialize JS Plugins for this scope
     * @param targetSelector
     * @param targetElement
     * @param requestData
     * @param resultCode
     * @param response
     */
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

}