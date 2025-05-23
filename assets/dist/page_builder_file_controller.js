import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';

export default class extends Controller {
    static targets = [
        'uploadField',
    ];

    connect() {
        console.log('✅ PageBuilder controller loaded')
    }

    async uploadFile(event)
    {
        const file = event.target.files;
        const uploadUrl = event.currentTarget.dataset.url;
        const maxFiles = event.currentTarget.dataset.maxFiles;
        const index = event.currentTarget.dataset.index;
        const key = event.currentTarget.dataset.key;
        const instanceId = event.currentTarget.dataset.instanceId;
        const type = event.currentTarget.dataset.type;

        if(!file)
        {
            alert('No file found');
            return;
        }

        if(file.length > maxFiles ?? 1)
        {
            alert('Exceeded total allowable number of files. Maximum is ' + maxFiles);
        }

        let formData = new FormData();
        for (const file of event.target.files) {
            formData.append('files[]', file);
        }

        try {
            const response = await $.ajax({
                url: uploadUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            });

            if(response["uploadedFilename"] === '')
            {
                alert('Error in upload, the Controller did not return a new filename.');
                return;
            }

            this.dispatch("replaceOverride", {
                prefix: false,
                target: window,
                detail: {
                    instanceId: instanceId,
                    type: 'image',
                    value: response["uploadedFilename"],
                    index: index,
                    key: key,
                }
            });

            window.dispatchEvent(new CustomEvent('notification:create', {
                detail: {
                    title: 'Success',
                    text: response["text"],
                    type: 'success'
                }
            }));
        } catch(e) {
            window.dispatchEvent(new CustomEvent('notification:create', {
                detail: {
                    title: e.title,
                    text: e.text,
                    type: 'error'
                }
            }));
        }
    }
}