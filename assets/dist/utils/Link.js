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

    connect() {
        this.linkContainers.forEach((container) => {
            const inputs = this.getInputs(container)
            const activeKey = this.controller.contextValue.context?.activeKey;
            const activeValue = this.controller.contextValue.context?.activeValue;
            const selectedType = activeKey?.match(/\[(.*?)\]/)?.[1];

            if (selectedType && this.typePossibilities().includes(selectedType)) {
                inputs.typeSelect.value = selectedType;
                this.switchTypeFor(container, selectedType);

                requestAnimationFrame(() => {
                    const trySet = () => {
                        const updatedInputs = this.getInputs(container);

                        if (
                            (selectedType === "url" && updatedInputs.urlInput) ||
                            (selectedType === "path" && updatedInputs.pathInput) ||
                            (selectedType === "email" && updatedInputs.emailInput)
                        ) {
                            if (selectedType === "url") updatedInputs.urlInput.value = activeValue;
                            if (selectedType === "path") updatedInputs.pathInput.value = activeValue;
                            if (selectedType === "email") updatedInputs.emailInput.value = activeValue;
                        } else {
                            // Try again shortly
                            setTimeout(trySet, 50);
                        }
                    };

                    trySet();
                });
            }
        });
    }

    typePossibilities() {
        return ['url', 'path', 'email'];
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

        // Optional: Clear other values
        if (type === "path") inputs.urlInput.value = inputs.emailInput.value = "";
        if (type === "url") inputs.pathInput.value = inputs.emailInput.value = "";
        if (type === "email") inputs.pathInput.value = inputs.urlInput.value = "";
    }
}
