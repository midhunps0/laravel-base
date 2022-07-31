<x-dashboard-base :ajax="$x_ajax">
    <div class="p-6 border-b border-base-200">
        <h3 class="text-xl font-bold">Import Trade Backup</h3>
        <div class="mt-4">
            <form x-data="{
                file: null,
                processing: false,
                finished: false,
                message: 'Import completed.',
                uploadResult: {
                    status: false,
                    totalItems: 0,
                    failedItems: []
                },
                setFile(files) {
                    if (files == null) {
                        this.file = null;
                    } else {
                        this.file = files[0];
                    }
                },
                doSubmit() {
                    var formData = new FormData();
                    formData.append('file', this.file, 'tbk.xlsx');
                    this.processing = true;
                    this.uploadResult = {
                        status: false,
                        totalItems: 0,
                        failedItems: []
                    }
                    axios({
                        method: 'post',
                        url: '{{route("post.import.trade_backup")}}',
                        data: formData,
                        headers: { 'Content-Type': 'multipart/form-data' },
                      }).then((r) => {
                        console.log(r);
                        if (r.data.success) {
                            this.message = 'Import finished.';
                            this.uploadResult.totalItems = r.data.total_items;
                            this.uploadResult.failedItems = r.data.failed_items;
                        } else {
                            this.message = 'Opps! Import failed unexpectedly. Please make sure you have all the required column titles.';
                        }

                        this.uploadResult.status = r.data.success;
                        this.processing = false;
                        this.finished = true;
                        this.file = null;
                    });
                }
            }"
            x-init="
                file = null;
                processing = false;
                finished = false;
                message = 'Import completed.';
            "
                @filechange="setFile($event.detail.files); console.log('file changed');"
                action="#"
                class="relative">
                <label class="border-2 border-base-200 p-3 flex flex-row items-center w-full rounded cursor-pointer my-2" for="customFile" x-data="{ files: null }"
                @filesReset.window="files = null; console.log('files cancelled');"
                >
                    <button x-show="files != null"
                        @click.prevent.stop="files = null; $dispatch('filechange', {files: files});"
                        class="p-1 border border-error text-error mr-2"><x-display.icon icon="icons.close"/></button>
                    <input type="file" class="sr-only" id="customFile" @change="files = Object.values($event.target.files); $dispatch('filechange', {files: files});">
                    <span x-text="files ? files.map(file => file.name).join(', ') : 'Choose single file...'"></span>
                </label>
                <div x-show="file != null">
                    <button @click.prevent.stop="doSubmit" class="btn btn-sm text-accent">Import File</button>
                </div>
                <div x-show="processing" class="absolute z-10 top-0 left-0 w-full h-full bg-opacity-50 bg-base-200 flex flex-row items-center justify-center">
                    <span class="animate-pulse">processing..</span>
                </div>
                <div x-show="finished" class="absolute z-10 top-0 left-0 w-full h-full p-14 bg-opacity-50 bg-base-200">
                    <div class="flex flex-col items-center justify-center p-3 bg-base-200 border border-base-300 rounded-md shadow">
                        <div class="p-2 mb-3" x-text="message"></div>
                        <div class="mb-3">
                            <h3 class="font-bold text-center p-2 mb-2 text-success"><span x-text="uploadResult.totalItems - uploadResult.failedItems.length"></span>&nbsp; out of <span x-text="uploadResult.totalItems"></span>&nbsp; items imported successfully.</h3>
                            <div x-show="uploadResult.failedItems.length > 0" class="p-2">
                                <h3 class="font-bold text-center p-2 mb-2 text-warning">Failed items:</h3>
                                <div class="max-h-80 overflow-y-scroll">
                                    <table class="table table-mini border border-base-200">
                                        <thead>
                                            <tr class="sticky">
                                                <th>No.</th>
                                                <th>Client Code</th>
                                                <th>Client Status</th>
                                                <th>Script Symbol</th>
                                                <th>Script Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <template x-for="(item, index) in uploadResult.failedItems">
                                            <tr>
                                                <td x-text="index+1"></td>
                                                <td x-text="item.client_code"></td>
                                                <td x-text="item.client"></td>
                                                <td x-text="item.script_symbol"></td>
                                                <td x-text="item.script"></td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <button @click.prevent.stop="finished = false;" class="btn btn-warning">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-base>