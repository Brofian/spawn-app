import Plugin from "Plugin";

export default class AccordionPlugin extends Plugin {


    init() {
        this.toggleSelectorClass = 'js-accordion';
        this.bodySelectorClass = 'js-accordion-body';
        this.toggleStateClass = 'js-accordion-open';

        //search toggle
        if(this._element.classList.contains(this.toggleSelectorClass)) {
            this.toggle = this._element;
        }
        else {
            this.toggle = this._element.querySelector('.'+this.toggleSelectorClass);
        }


        //search body
        this.body = this.toggle.parentNode.querySelector('.'+this.bodySelectorClass);

        //check elements
        if(!this.toggle || !this.body) {
            return;
        }


        this.registerEventListeners();
    }


    registerEventListeners() {
        let event = this._isTouchDevice ? 'touch' : 'click';

        this.toggle.addEventListener(event, this.onToggleClick.bind(this));

    }


    onToggleClick() {

        this.body.style.maxHeight = this.body.scrollHeight + "px";

        if(this.body.classList.contains(this.toggleStateClass)) {
            //closing
            this.body.classList.remove(this.toggleStateClass);
        }
        else {
            //opening
            this.body.classList.add(this.toggleStateClass);
        }

    }



}