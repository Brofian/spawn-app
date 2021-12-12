import Plugin from 'Plugin';
import DeviceManager from 'DeviceManager';
import AjaxDataReplace from './ajaxDataReplace.function';

export default class BackendContentLinkPlugin extends Plugin {


    init() {
        this.backendLinkUrlData = 'backendContentLink';
        this.backendTargetData = 'backendContentTarget';
        this.backendLinkUrlRewriteData = 'backendContentLinkUriRewrite';
        this.contentTargetSelector ='#backend_content';

        this.registerEvent();
    }


    registerEvent() {
        let event = DeviceManager.isTouchDevice() ? 'touch' : 'click';

        let targetSelector = this.contentTargetSelector;
        if(this._element.dataset[this.backendTargetData]) {
            targetSelector = this._element.dataset[this.backendTargetData];
        }

        let addEntryToUri = this._element.dataset[this.backendLinkUrlRewriteData] === 'true';

        this._element.addEventListener(event, this.onSidebarLinkClick.bind(this, targetSelector, addEntryToUri));
    }


    onSidebarLinkClick(targetSelector, addEntryToUri, event) {

        let url = this._element.dataset[this.backendLinkUrlData];

        window.history.pushState('', '', url);

        if(targetSelector && url) {
            AjaxDataReplace.loadAndReplaceContent(url, targetSelector);
        }

    }





}