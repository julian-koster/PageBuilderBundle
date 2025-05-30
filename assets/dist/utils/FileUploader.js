import $ from 'jquery';

export class FileUploader {
    constructor(controller) {
        this.controller = controller
    }

    async uploadFile(event) {
        const { dataset } = event.target
        const files = event.target.files
        const uploadField = this.controller.uploadFieldTarget;
        const uploadUrl = dataset.url
        const maxFiles = dataset.maxFiles;
        const index = dataset.index;
        const key = dataset.key;
        const instanceId = dataset.instanceId;
        const type = dataset.type;


        if (!files.length) {
            alert('No files found')
            return
        }

        if(files.length > maxFiles ?? 1)
        {
            alert('Exceeded total allowable number of files. Maximum is ' + maxFiles);
        }

        const formData = new FormData()
        for (const file of files) {
            formData.append('files[]', file)
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

            this.controller.dispatch("replaceOverride", {
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

            window.dispatchEvent(new CustomEvent('replaceOverride', {
                detail: {
                    instanceId,
                    type: 'image',
                    value: response["uploadedFilename"],
                    index,
                    key,
                },
                bubbles: true,
            }));

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