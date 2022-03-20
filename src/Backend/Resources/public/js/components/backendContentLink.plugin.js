import Plugin from 'Plugin';
import DeviceManager from 'DeviceManager';
import AjaxDataReplace from './ajaxDataReplace.function';

export default class BackendContentLinkPlugin extends Plugin {

    static initializedBackEvent = false;

    init() {
        this.backendLinkUrlData = 'backendContentLink';
        this.backendTargetData = 'backendContentTarget';
        this.backendLinkUrlRewriteData = 'backendContentLinkUriRewrite';
        this.contentTargetSelector ='#backend_content';

        if(!BackendContentLinkPlugin.initializedBackEvent) {
            window.addEventListener('popstate', this.registerBackEvent.bind(this))
            BackendContentLinkPlugin.initializedBackEvent = true;
        }

        this.registerEvent();
    }


    registerBackEvent(event) {
        let targetSelector = history.state.selector;
        let page = history.state.page;

        AjaxDataReplace.loadAndReplaceContent(page, targetSelector);

        event.preventDefault();
    }

    registerEvent() {
        let event = DeviceManager.isTouchDevice() ? 'touch' : 'click';

        let targetSelector = this.contentTargetSelector;
        if(this._element.dataset[this.backendTargetData]) {
            targetSelector = this._element.dataset[this.backendTargetData];
        }

        let addEntryToUri = this._element.dataset[this.backendLinkUrlRewriteData] !== 'false';

        this._element.addEventListener(event, this.onSidebarLinkClick.bind(this, targetSelector, addEntryToUri));
    }


    onSidebarLinkClick(targetSelector, addEntryToUri, event) {

        let url = this._element.href;
        if(!url) {
            url = this._element.dataset[this.backendLinkUrlData];
        }

        if(addEntryToUri) {
            var state = {
                name: document.title,
                page: url,
                selector: targetSelector
            };
            window.history.pushState(state, '', url);
        }

        if(targetSelector && url) {
            AjaxDataReplace.loadAndReplaceContent(url, targetSelector);
        }

        event.preventDefault();
    }





}