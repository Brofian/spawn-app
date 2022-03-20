import CustomSelect from './customSelect.plugin';

export default class EntitySelect extends CustomSelect {

    init() {
        //init variables
        this.options = [];
        let internalName = this._element.dataset.entitySelectName;
        this.action = '/backend/api/config/entity/' + internalName;
        this.searchTimeout = null;
        this.getOptions();
        this.getCurrentValue();
        this.initElement();

        document.addEventListener('click', this.onClickPage.bind(this));
        this.labelContainer.addEventListener('click', this.onClickLabel.bind(this), true);
    }

    getInputName() {
        return this._element.dataset.entitySelect;
    }

    onSearchInputChanged() {
        if(this.searchTimeout) {
            window.clearTimeout(this.searchTimeout);
        }

        this.searchTimeout = window.setTimeout((function() {
            let length = this.searchContainer.value.length;
            if(length > 2 || length === 0) {
                this.getOptions();
            }
        }).bind(this), 500);
    }

    onAjaxResponse(response) {
        // read response
        this.options = [];

        try {
            let parsedResponse = JSON.parse(response);
            for(let entity of parsedResponse.entities) {
                this.options.push({
                    'value': entity.identifier,
                    'label': entity.label
                });
            }
        } catch(e) {

        }


        this.refreshOptionElements();
    }


    beforeOptionRefresh() {
        if(!this.searchContainer) {
            this.searchContainer = document.createElement('input');
            this.searchContainer.type = 'text';
            this.searchContainer.classList.add('js-entity-select-search');
            this.searchContainer.addEventListener('keyup', this.onSearchInputChanged.bind(this));
            this.optionsContainer.appendChild(this.searchContainer);
        }

        let options = this.optionsContainer.querySelectorAll('.js-entity-select-option');
        for (let option of options) {
            this.optionsContainer.removeChild(option);
        }
    }

    getOptions() {
        let data = {};
        if(this.searchContainer) {
            data = {
                search: this.searchContainer.value
            };
        }

        //load url content
        $.ajax(
            {
                url: this.action,
                type: 'post',
                data: data,
                async: true,
                cache: true,
            }
        ).done(
            this.onAjaxResponse.bind(this)
        );
    }


    getCurrentValue() {
        this.currentValue = this._element.dataset.entitySelectValue;
        this.currentLabel = this._element.dataset.entitySelectLabel;
    }

}