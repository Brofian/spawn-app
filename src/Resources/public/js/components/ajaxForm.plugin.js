import Plugin from 'Plugin';
import LoadingSpinner from "./loadingSpinner.function";
import FlashHint from "./flashHint.function";


export default class AjaxFormPlugin extends Plugin {

    init() {
        this.action = this._element.action;
        this.method = this._element.method;

        if(!['POST', 'GET', 'PUT', 'DELETE'].includes(this.method)) {
            this.method = 'POST';
        }

        this._element.addEventListener('submit', this.onSubmitForm.bind(this), true);
    }

    getFormValues() {
        let inputs = this._element.querySelectorAll('[name]');
        let values = {};
        for(let input of inputs) {

            let value = null;
            switch(input.type) {
                case 'checkbox':
                    value = input.checked;
                    break;
                default:
                    value = input.value;
                    break;
            }

            if(value !== null) {
                values[input.name] = value;
            }
        }

        return values;
    }


    onSubmitForm(event) {
        event.preventDefault();
        event.stopPropagation();

        LoadingSpinner.createLoadingSpinner(this._element);

        let data = this.getFormValues();
        console.log(data);

        //load url content
        $.ajax(
            {
                url: this.action,
                type: this.method,
                data: data,
                async: true,
                cache: false,
            }
        ).done(
            this.onAjaxFormSubmitResult.bind(this)
        );
    }

    onAjaxFormSubmitResult(data) {
        try {       data = JSON.parse(data);    }
        catch(e) {  data = {success: false};    }

        if(data.success) {
            FlashHint.createFlashHint('Success!!', 'success');
        }
        else if(data.errors && data.errors.length) {
            for(let error of data.errors) {
                FlashHint.createFlashHint(error, 'error');
            }
        }
        else {
            FlashHint.createFlashHint('Could not submit form!', 'error');
        }

        LoadingSpinner.removeLoadingSpinner(this._element);
    }

}