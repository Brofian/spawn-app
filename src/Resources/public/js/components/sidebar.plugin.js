import Plugin from 'Plugin';
import DeviceManager from 'DeviceManager';
import AjaxDataReplace from './ajaxDataReplace.plugin';

export default class SidebarPlugin extends Plugin {


    init() {
        this.sidebarLinkSelector = '.js-sidebar-link';
        this.sidebarLinkUrlData = 'sidebarLink';

        this.sidebarLinks = this._element.querySelectorAll(this.sidebarLinkSelector);

        this.registerEvents();
    }


    registerEvents() {
        let event = DeviceManager.isTouchDevice() ? 'touch' : 'click';

        for(let sidebarLink of this.sidebarLinks) {
            sidebarLink.addEventListener(event, this.onSidebarLinkClick.bind(this));
        }
    }


    onSidebarLinkClick(event) {

        let element = event.target;

        let url = element.dataset[this.sidebarLinkUrlData];

        AjaxDataReplace.loadAndReplaceContent(url, '#content');

    }





}