import Plugin from 'Plugin';
import DeviceManager from 'DeviceManager';
import AjaxDataReplace from './ajaxDataReplace.plugin';

export default class BackendContentLinkPlugin extends Plugin {


    init() {
        this.backendLinkSelector = '[data-backend-content-link]';
        this.backendLinkUrlData = 'backendContentLink';
        this.backendTargetData = 'backendContentTarget';
        this.backendLinkUrlRewriteData = 'backendContentLinkUriRewrite';
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

            let addEntryToUri = sidebarLink.dataset[this.backendLinkUrlRewriteData] === 'true';

            sidebarLink.addEventListener(event, this.onSidebarLinkClick.bind(this, targetSelector, addEntryToUri));
        }
    }


    onSidebarLinkClick(targetSelector, addEntryToUri, event) {

        let element = event.target;

        let url = element.dataset[this.backendLinkUrlData];

        window.history.pushState('', '', url);

        AjaxDataReplace.loadAndReplaceContent(url, targetSelector);
    }





}