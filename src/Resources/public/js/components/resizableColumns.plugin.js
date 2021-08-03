import Plugin from "Plugin";

export default class ResizeableColumnsPlugin extends Plugin {


    init() {
        this.headColSelector = 'thead tr th:not(:last-of-type)';
        this.bodyRowSelector = 'tbody tr';
        this.bodyRowColSelector = 'td:not(:last-of-type)';
        this.cols = [];

        this.prepareHeaderColumns();
        this.gatherBodyColumns();
    }

    prepareHeaderColumns() {
        let headerCols = this._element.querySelectorAll(this.headColSelector);

        //TODO:: check if event listener is working

        let cols = [];
        let id = 0;
        for(let headerCol of headerCols) {
            let columnGrabber = document.createElement('span');
            columnGrabber.classList.add('js-resizable-columns-grabber');
            columnGrabber.addEventListener('drag', this.onColumnDragged.bind(this, id));

            headerCol.appendChild(columnGrabber);
            cols.push(headerCol);
            id++;
        }
        this.cols.push(cols);
    }

    gatherBodyColumns() {
        let bodyRows = this._element.querySelectorAll(this.bodyRowSelector);

        for(let bodyRow of bodyRows) {

            let cols = [];
            for(let bodyRowCol of bodyRow.querySelectorAll(this.bodyRowColSelector)) {
                cols.push(bodyRowCol);
            }
            this.cols.push(cols);
        }
    }


    onColumnDragged(columnId) {
        //TODO:: resize all columns when dragged (including header column)
        console.log(columnId);
    }

}