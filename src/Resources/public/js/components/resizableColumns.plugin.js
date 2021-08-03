import Plugin from "Plugin";

export default class ResizeableColumnsPlugin extends Plugin {

    init() {
        this.headColSelector = 'thead tr th:not(:last-of-type):not(:first-of-type)';
        this.bodyRowSelector = 'tbody tr';
        this.bodyRowColSelector = 'td:not(:last-of-type):not(:first-of-type)';
        this.cols = [];
        this.currentDrag = -1;
        this.startWidth = -1;
        this.startMouseX = -1;

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
            columnGrabber.addEventListener('mousedown', this.onColumnMouseDown.bind(this, id));
            headerCol.addEventListener('mousemove', this.onColumnMouseMove.bind(this, id));
            headerCol.addEventListener('mouseup', this.onColumnMouseUp.bind(this, id));
            headerCol.addEventListener('mouseleave', this.onColumnMouseUp.bind(this, id));

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


    onColumnMouseDown(columnId, event) {
        this.currentDrag = columnId;
        this.startWidth = this.cols[0][columnId].getBoundingClientRect().width;
        this.startMouseX = event.clientX;
    }

    onColumnMouseMove(columnId, event) {
        if(columnId === this.currentDrag) {
            let currentMouseX = event.clientX;
            let mouseXDiff = this.startMouseX - currentMouseX;
            let newWidth = this.startWidth - mouseXDiff;

            console.log(newWidth + " / " + this.startWidth);

            for(let row of this.cols) {
                row[columnId].style.maxWidth = newWidth + "px";
            }
        }
    }

    onColumnMouseUp(columnId) {
        this.currentDrag = -1;
    }


}