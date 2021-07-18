export default class AjaxDataReplace {


    static loadAndReplaceContent(contentUrl, targetSelector) {

        //load url content
        $.ajax(
            {
                url: contentUrl
            }
        ).done(
            this.replaceContent.bind(null, targetSelector)
        );

    }

    static replaceContent(targetSelector, requestData, resultCode, response) {


        let container = document.createElement('template');
        container.innerHTML = requestData.trim();
        let urlContentElement = container.content.querySelector(targetSelector);

        let oldDOMElement = document.querySelector(targetSelector);

        if(!oldDOMElement || !urlContentElement) {
            return;
        }

        oldDOMElement.parentNode.replaceChild(urlContentElement, oldDOMElement);
    }




}