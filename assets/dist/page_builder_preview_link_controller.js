import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['type', 'url', 'path', 'email'];

    static values = {
        'context': Object
    }

    typePossibilities()
    {
        return ['url', 'path', 'email'];
    }

    connect()
    {
        this.setActiveField();
    }

    setActiveField() {
        const activeKey = this.contextValue.context?.activeKey;
        const activeValue = this.contextValue.context?.activeValue;
        const selectedType = activeKey?.match(/\[(.*?)\]/)?.[1];
        console.log(selectedType);


        if (selectedType && this.typePossibilities().includes(selectedType)) {
            // Step 1: update visibility
            this.typeTarget.value = selectedType;
            this.switchType({ currentTarget: { value: selectedType } });

            // Step 2: wait for visibility change, then apply value
            requestAnimationFrame(() => {
                if (selectedType === "url" && this.hasUrlTarget) {
                    this.urlTarget.value = activeValue;
                }

                if (selectedType === "path" && this.hasPathTarget) {
                    this.pathTarget.value = activeValue;
                }

                if (selectedType === "email" && this.hasEmailTarget) {
                    this.emailTarget.value = activeValue;
                }
            });
        }
    }

    updateLink(event)
    {
        let value = event.currentTarget.value;
        let key = event.currentTarget.dataset.key;
        let type = event.currentTarget.dataset.type;
        let instanceId = event.currentTarget.dataset.instanceId;

        this.dispatch('replaceOverride', {
            prefix: false,
            target: window,
            detail: {
                type: 'link',
                value: value,
                instanceId: instanceId,
                key: key+'['+type+']',
            }
        })
    }

    switchType(event)
    {
        let type = event.currentTarget.value;

        if(type === null || type === "")
        {
            console.warn('Empty type received when switching between link configuration fields (url, path, email). The type should not be empty.');
        }

        if(!type in this.typePossibilities())
        {
            console.warn('Invalid path field type provided. Expecting ' + this.typePossibilities().join(', ') + ' but received: ' + type);
        }

        this.emailTarget.classList.toggle('hidden', type !== "email");
        this.urlTarget.classList.toggle('hidden', type !== "url");
        this.pathTarget.classList.toggle('hidden', type !== "path");

        // Reset all other values, we don't want double values for the link.
        if (type === "route") this.urlTarget.value = this.emailTarget.value = ""
        if (type === "url") this.pathTarget.value = this.emailTarget.value = ""
        if (type === "email") this.pathTarget.value = this.urlTarget.value = ""
    }
}