import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    static values = {
        key: String,
        instanceId: String,
    }

    async connect()
    {
        this.component = await getComponent(this.element);
    }

    updateLayoutConfig(event) {
        console.log(event)
        let value, key, instanceId;

        if(event.detail) {
            ({ key, value, instanceId } = event.detail);
        }
        else {
            key = event.currentTarget.dataset.key;
            value = event.currentTarget.value;
            instanceId = event.currentTarget.dataset.instanceId;
        }

        if(!value)
        {
            console.warn('Updating the blockInstance layout config with an empty value as the value for key: ' + key + ' is empty.');
        }

        if(!key)
        {
            console.warn('Skipping updating blockInstance layout config as the key (e.g. margin-right (mr)) is empty.');
        }

        if(!instanceId)
        {
            console.warn('Cannot update blockInstance layout config as the instanceId is empty.');
        }

        this.component.action('updateLayoutConfig', {
            key: key,
            value: value,
            instanceId: instanceId,
        });
    }

    updateOverride(event) {
        let type, value, key, instanceId;

        if (event.detail) {
            ({ type, value, key, instanceId } = event.detail);
        } else {
            type = event.currentTarget.dataset.type;
            value = event.currentTarget.value;
            instanceId = event.currentTarget.dataset.instanceId;
            key = event.currentTarget.dataset.key;
        }

        if (type === 'list') {
            return this.updateNestedOverride(event);
        }

        if (!key) {
            console.warn('Skipping override update due to empty key');
            return;
        }

        this.component.action('updateOverride', {
            instanceId: instanceId,
            key: key+'['+type+']',
            value: value,
            type: type,
        });
    }

    updateNestedOverride(event) {
        const key = event.currentTarget.dataset.key;
        const instanceId = event.currentTarget.dataset.instanceId;
        const type = event.currentTarget.dataset.type;

        if (!key) {
            console.warn('Skipping override update due to empty key');
            return;
        }

        if(type !== "list") {
            console.warn('Skipping override as the provided type is not a list but a:' + type);
            return;
        }

        const itemKey = event.currentTarget.dataset.itemKey;
        const value = event.currentTarget.value;

        this.component.action('updateNestedOverride', {
            key: key+'['+type+']',
            itemKey: itemKey,
            value: value,
            instanceId: instanceId,
            type: type,
        });
    }

    replaceOverride(event) {
        let type, value, key, instanceId;

        if (event.detail) {
            ({ type, value, key, instanceId } = event.detail);
        } else {
            type = event.currentTarget.dataset.type;
            value = event.currentTarget.value;
            instanceId = event.currentTarget.dataset.instanceId;
            key = event.currentTarget.dataset.key;
        }

        if (!key) {
            console.warn('Skipping override replacement due to empty key');
            return;
        }

        this.component.action('replaceOverride', {
            instanceId: instanceId,
            key: key+'['+type+']',
            value: value,
            type: type,
        });
    }

    addListItem(event) {
        const key = event.currentTarget.dataset.key;
        const instanceId = event.currentTarget.dataset.instanceId;

        this.component.action('addListItem', {
            instanceId: instanceId,
            key: key
        });
    }
}