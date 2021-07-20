import Plugin from 'Plugin';
import DeviceManager from 'DeviceManager';
import AjaxDataReplace from './ajaxDataReplace.plugin';

export default class BackendContentLinkPlugin extends Plugin {


    init() {
        this.backendLinkSelector = '[data-backend-content-link]';
        this.backendLinkUrlData = 'backendContentLink';
        this.backendTargetData = 'backendContentTarget';
        this.contentTargetSelector ='#backend_content';

        this.sidebarLinks = this._element.querySelectorAll(this.backendLinkSelector);

        this.registerEvents();
    }


    registerEvents() {
        let event = DeviceManager.isTouchDevice() ? 'touch' : 'click';

        for(let sidebarLink of this.sidebarLinks) {

            let targetSelector = this.contentTargetSelector;
            if(sidebarLink.dataset[this.backendTargetData]) {
                targetSelector = sidebarLink.dataset[this.backendTargetData];
            }

            sidebarLink.addEventListener(event, this.onSidebarLinkClick.bind(this, targetSelector));
        }
    }


    onSidebarLinkClick(targetSelector, event) {

        let element = event.target;

        let url = element.dataset[this.backendLinkUrlData];

        AjaxDataReplace.loadAndReplaceContent(url, targetSelector);
    }





}