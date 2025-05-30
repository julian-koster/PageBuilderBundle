export class Link {
    constructor(controller) {
        this.controller = controller
    }

    get linkContainers() {
        return this.controller.linkContainerTargets;
    }

    getInputs(container) {
        return {
            typeSelect: container.querySelector('[data-index="type"]'),
            urlInput: container.querySelector('[data-index="url"]'),
            emailInput: container.querySelector('[data-index="email"]'),
            pathInput: container.querySelector('[data-index="path"]'),
        }
    }

    updateLink(event) {
        const { dataset } = event.target;
        const value = event.target.value;
        const key = dataset.key;
        const type = dataset.type;
        const instanceId = dataset.instanceId;

        this.controller.dispatch('replaceOverride', {
            prefix: false,
            target: window,
            detail: {
                type: 'link',
                value: value,
                instanceId: instanceId,
                key: `${key}[${type}]`,
            }
        })
    }

    switchType(event) {
        const type = event.currentTarget.value;
        const container = event.currentTarget.closest('[data-juliankoster--pagebuilderbundle--page-builder-target="linkContainer"]');
        this.switchTypeFor(container, type);
    }

    switchTypeFor(container, type) {
        const inputs = this.getInputs(container);
        inputs.emailInput.classList.toggle('hidden', type !== "email");
        inputs.urlInput.classList.toggle('hidden', type !== "url");
        inputs.pathInput.classList.toggle('hidden', type !== "path");
    }
}
