import Plugin from 'Plugin';
import EventManager from "EventManager";


export default class CustomSelect extends Plugin {

    static options = {
        'changeEventName': 'customSelectChanged'
    }

    init() {
        //init variables
        this.getOptions();
        this.getCurrentValue();
        this.initElement();

        document.addEventListener('click', this.onClickPage.bind(this));
        this.labelContainer.addEventListener('click', this.onClickLabel.bind(this), true);
    }

    getOptions() {
        let optionElements = this._element.querySelectorAll('option');
        this.optionList = [];
        for(let optionEl of optionElements) {
            let value = optionEl.value;
            if(!value) {
                value = optionEl.innerText;
            }

            this.optionList.push({
                'value': value,
                'label': optionEl.innerText,
                'selected': !!optionEl.selected,
                'dataset': optionEl.dataset
            });
        }
    }

    getCurrentValue() {
        this.currentValue = '';
        this.currentLabel = '';

        if(this.optionList) {
            let isFirst = true;
            for(let option of this.optionList) {
                if(isFirst || option.selected) {
                    isFirst = false;
                    this.currentValue = option.value;
                    this.currentLabel = option.label;

                    if(option.selected) {
                        break;
                    }
                }
            }
        }
    }


    initElement() {
        let newSelectElement = document.createElement('div');
        newSelectElement.classList.add('js-entity-select-element');

        this.labelContainer = document.createElement('div');
        this.labelContainer.classList.add('js-entity-select-label');
        newSelectElement.appendChild(this.labelContainer);

        this.valueContainer = document.createElement('input');
        this.valueContainer.classList.add('js-entity-select-options');
        this.valueContainer.type = 'hidden';
        this.valueContainer.name = this.getInputName();
        newSelectElement.appendChild(this.valueContainer);

        this.setNewValue(this.currentValue, this.currentLabel);

        this.optionsContainer = document.createElement('div');
        this.optionsContainer.classList.add('js-entity-select-options');
        this.refreshOptionElements();
        newSelectElement.appendChild(this.optionsContainer);

        this._element.parentElement.replaceChild(newSelectElement, this._element);
        this._element = newSelectElement;
    }

    getInputName() {
        return this._element.name;
    }

    beforeOptionRefresh() {
        this.optionsContainer.textContent = '';
    }
    afterOptionRefresh() {
        EventManager.publish('pluginmanager.startInitializeScope', [this.optionsContainer]);
    }

    refreshOptionElements() {
        this.beforeOptionRefresh();

        for(let option of this.optionList) {
            let optionEl = document.createElement('span');
            optionEl.classList.add('js-entity-select-option');
            optionEl.innerText = option.label;
            optionEl.dataset.jsEntitySelectOptionValue = option.value;
            if(typeof option.dataset === "object") {
                for(let [key,value] of Object.entries(option.dataset)) {
                    optionEl.dataset[key] = value;
                }
            }

            optionEl.addEventListener('click', this.onClickOption.bind(this), true);
            this.optionsContainer.appendChild(optionEl);
        }

        this.afterOptionRefresh();
    }

    setNewValue(value, label) {
        this.valueContainer.value = value;
        this.labelContainer.innerText = label;
    }


    onClickPage(event) {
        if(!this._element.classList.contains('opened')) {
            return;
        }

        if (!this._element.contains(event.target)) {
            this._element.classList.remove('opened')
        }
    }

    onClickLabel() {
        if(this._element.classList.contains('opened')) {
            this._element.classList.remove('opened');
        }
        else {
            this._element.classList.add('opened');
        }
    }

    onClickOption(event) {
        let target = event.target;
        let label = target.innerText;
        let value = target.dataset.jsEntitySelectOptionValue;

        this._element.classList.remove('opened');
        this.setNewValue(value, label);
        EventManager.publish(this.options.changeEventName, {
            element: this._element,
            value: value,
            label: label
        });
    }


}