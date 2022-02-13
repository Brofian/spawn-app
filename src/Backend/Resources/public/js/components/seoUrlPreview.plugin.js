import Plugin from "Plugin";
import String from "./String.function";

export default class SeoUrlPreviewPlugin extends Plugin {

    init() {
        this.input = this._element.querySelector('[data-seo-url-editor-input]');
        this.preview = this._element.querySelector('[data-seo-url-editor-preview]');
        this.collection = this._element.querySelector('[data-seo-url-editor-collection]');
        try {
            this.collectionData = JSON.parse(this._element.dataset.seoUrlEditor);
        }
        catch(e) {
            this.collectionData = {};
        }


        if(!this.input || !this.preview) {
            return;
        }

        this.input.addEventListener('input', this.updatePreviewHtml.bind(this));
        this.updatePreviewHtml();
    }


    updatePreviewHtml() {
        let cUrl = this.input.value;
        cUrl = this.encodeInput(cUrl);

        var count = (cUrl.match(/\/{}/g) || []).length;
        for(let param of this.collectionData) {
            cUrl = cUrl.replace('/{}', '/'+this.dataToFlagHtml(param.name, param.required).outerHTML);
        }

        this.updateCollectionHtml(count);
        this.preview.innerHTML = cUrl;
    }

    updateCollectionHtml(skippedElements = 0) {
        let collectionElements = [];

        let index = 0;
        for(let param of this.collectionData) {
            index++;

            if(index <= skippedElements) {
                continue;
            }
            collectionElements.push(this.dataToFlagHtml(param.name, param.required));
        }


        this.collection.innerHTML = '';
        for(let collectionElement of collectionElements ) {
            this.collection.appendChild(collectionElement);
        }
    }


    dataToFlagHtml(value, required) {
        let flag = document.createElement('span');
        flag.innerText = value;
        flag.classList.add('inline-flag');
        if(required) {
            flag.classList.add('inline-flag-highlight');
        }
        flag.addEventListener('click', this.onCollectionItemClicked.bind(this));

        return flag;
    }

    encodeInput(value) {

        let mapping = {
            '\/': '_-_',
            '\{\}': '_--_'
        };

        for(let char in mapping) {
            value = value.replaceAll(char, mapping[char]);
        }

        value = encodeURIComponent(value);

        for(let char in mapping) {
            value = value.replaceAll(mapping[char], char);
        }

        return value;
    }

    onCollectionItemClicked(event) {
        let value = this.input.value;
        value = String.trimRight(value, ' \r\n\t\/\{') + '/{}';
        this.input.value = value;

        this.updatePreviewHtml();
    }

}