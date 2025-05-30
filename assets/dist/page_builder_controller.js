import { Controller } from '@hotwired/stimulus'
import { getComponent } from '@symfony/ux-live-component'
import { FileUploader } from './utils/FileUploader.js'
import { PreviewApp } from './PreviewApp.js'
import { RemoveClassesTomSelect } from "./utils/RemoveClassesTomSelect.js"
import { Dropdown } from "./utils/Dropdown.js"
import { Link } from "./utils/Link.js"

export default class extends Controller {
    static values = {
        instanceId: String,
        key: String,
        structuredSubTypes: Array,
        context: Object,
    }

    static targets = ['uploadField', 'tomSelect', 'menu', 'dropdown', 'linkContainer']

    async connect() {
        this.component = await getComponent(this.element);
        this.uploader = new FileUploader(this)
        this.previewApp = new PreviewApp(this)
        this.removeClassesTomSelect = new RemoveClassesTomSelect(this)
        this.removeClassesTomSelect.cleanupTomSelect()
        this.dropdowns = []
        this.link = new Link(this)
        this.link.connect()

        this.dropdownTargets.forEach((dropdownEl) => {
            const button = dropdownEl.querySelector('button')
            const index = button?.dataset.index
            const menu = this.menuTargets.find(menu => menu.dataset.index === index)

            if (menu && index) {
                this.dropdowns[index] = new Dropdown(dropdownEl, menu)
            }
        })
    }

    uploadFile(event) {
        this.uploader.uploadFile(event)
    }

    updateOverride(event) {
        this.previewApp.updateOverride(event);
    }

    replaceOverride(event) {
        this.previewApp.replaceOverride(event);
    }

    toggleDropdown(event) {
        const index = event.currentTarget.dataset.index;
        this.dropdowns[index]?.toggle(event)
    }

    setActiveField(event) {
        this.link.setActiveField(event);
    }

    updateLink(event)
    {
        this.link.updateLink(event);
    }

    switchType(event)
    {
        this.link.switchType(event);
    }

    updateLayoutConfig(event)
    {
        this.previewApp.updateLayoutConfig(event);
    }
}