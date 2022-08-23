<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{ compact: $persist(false), mode: 'view' }" class="p-3 overflow-x-scroll">

        <div class="flex flex-col justify-between items-center mb-4">
            <div class="flex flex-row justify-between w-full pr-10 items-center">
                <div class="flex flex-row items-center mb-8">
                    <h3 class="text-xl font-bold text-left">Edit Script</h3>
                    <button @click.prevent.stop="mode == 'view' ? mode = 'edit' : mode = 'view';" class="btn btn-ghost" :class="mode == 'view' ? 'text-warning' : 'text-accent'">
                        <x-display.icon icon="icons.edit" height="h-6" width="w-6"/>
                    </button>
                </div>
                <a href="#" @click="$dispatch('linkaction', {link: '{{route('scripts.show', $script->id)}}', route: 'scripts.show'})" class="text-warning">Go Back</a>
            </div>

            <div>

            </div>
            <div class="flex-grow flex flex-row justify-end items-center space-x-4 w-full">
                <form x-data="{
                    isin_code: '',
                    symbol: '',
                    tracked: 0,
                    company_name: '',
                    industry: '',
                    series: '',
                    fno: 0,
                    nifty: 0,
                    nse_code: '',
                    bse_code: '',
                    yahoo_code: '',
                    doc: '',
                    bbg_ticker: '',
                    bse_security_id: '',
                    capitaline_code: '',
                    mvg_sector: '',
                    agio_indutry: '',
                    remarks: '',
                    result: 0,
                    error: '',
                    formValid() {
                        return true;
                    },
                    updateScript() {
                        let params = {
                                _method: 'PUT',
                                isin_code: this.isin_code,
                                symbol: this.symbol,
                                tracked: this.tracked,
                                company_name: this.company_name,
                                industry: this.industry,
                                series: this.series,
                                fno: this.fno,
                                nifty: this.nifty,
                                nse_code: this.nse_code,
                                bse_code: this.bse_code,
                                yahoo_code: this.yahoo_code,
                                doc: this.doc,
                                bbg_ticker: this.bbg_ticker,
                                bse_security_id: this.bse_security_id,
                                capitaline_code: this.capitaline_code,
                                mvg_sector: this.mvg_sector,
                                agio_indutry: this.agio_indutry,
                                remarks: this.remarks,
                            };

                        axios.post(
                            '{{route('scripts.update', $script->id)}}',
                            params
                        ).then((r) => {
                            if (r.data.success) {
                                this.result = 1;
                            } else {
                                this.result = -1;
                                console.log(Object.values(r.data.error));

                                let estr = Object.values(r.data.error).reduce((s, e) => {
                                    e.forEach((ers) => {
                                        if (s.length > 0) {
                                            return s+', '+ers;
                                        }
                                        else {
                                            return s + ers;
                                        }
                                    });
                                    console.log(e);
                                    return s;
                                });
                                console.log(estr);
                                this.error = estr;
                            }
                        }).catch((e) => {
                            this.result = -1;
                            this.error = 'Sorry, something went wrong.';
                            console.log(e);
                        });
                    }
                }"
                x-init="
                isin_code = '{{$script->isin_code}}';
                symbol = '{{$script->symbol}}';
                tracked = {{$script->tracked}};
                company_name = '{{$script->company_name}}';
                industry = '{{$script->industry}}';
                series = '{{$script->series}}';
                fno = {{$script->fno}};
                nifty = {{$script->nifty}};
                nse_code = {{$script->nse_code}};
                bse_code = {{$script->bse_code}};
                yahoo_code = '{{$script->yahoo_code}}';
                doc = '{{$script->doc}}';
                bbg_ticker = '{{$script->bbg_ticker}}';
                bse_security_id = '{{$script->bse_security_id}}';
                capitaline_code = {{$script->capitaline_code}};
                mvg_sector = '{{$script->mvg_sector}}';
                agio_indutry = '{{$script->agio_indutry}}';
                remarks = '{{$script->remarks}}';
                "
                action="" @submit.prevent.stop="updateScript();"
                class="p-4 border border-base-200 rounded-md w-full relative"
                >
                    <div x-show="result == 1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                            <div class="text-success">Script added successfully!</div>
                            <div class="flex flex-row justify-evenly space-x-4">
                                <a href="#" @click.prevent.stop="result = 0; $dispatch('linkaction', {link: '{{route('scripts.show', $script->id)}}', route: 'scripts.show', fresh: true})" class="btn btn-sm capitalize">Ok</a>
                            </div>
                    </div>
                    <div x-show="result == -1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                        <div class="text-error">Couldn't update the script:</div>
                        <div class="flex flex-col space-y-4 justify-center items-center space-x-4">
                            <span x-text="error"></span>
                            <button @click.prevent.stop="result = 0;" class="btn btn-sm btn-error capitalize">Ok</button>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start mb-3">
                        <div class="form-control w-96">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">ISIN Code</span>
                            </label>
                            <input x-model="isin_code" type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" required :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-52">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Symbol</span>
                            </label>
                            <input x-model="symbol" type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" required :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-52 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Tracked</span>
                            </label>
                            <select x-model="tracked" class="select select-bordered" :disabled="mode == 'view'">
                                <option value="0" disabled selected>Select</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-control w-52 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Company Name</span>
                            </label>
                            <input x-model="company_name" type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" required :readonly="mode == 'view'"/>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Industry</span>
                            </label>
                            <input x-model="industry"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Series</span>
                            </label>
                            <input x-model="series"  type="text" class="w-48 m-0 input input-bordered read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">FNO</span>
                            </label>
                            <select x-model="fno" class="select select-bordered" :disabled="mode == 'view'">
                                <option value="0" disabled selected>Select</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">NIFTY</span>
                            </label>
                            <select x-model="nifty" class="select select-bordered" :disabled="mode == 'view'">
                                <option value="0" disabled selected>Select</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">NSE Code</span>
                            </label>
                            <input x-model="nse_code"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">BSE Code</span>
                            </label>
                            <input x-model="bse_code"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Yahoo Code</span>
                            </label>
                            <input x-model="yahoo_code"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Doc</span>
                            </label>
                            <input x-model="doc"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">BBG Ticker</span>
                            </label>
                            <input x-model="bbg_ticker"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">BSE Security ID</span>
                            </label>
                            <input x-model="bse_security_id"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">

                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Capitaline Code</span>
                            </label>
                            <input x-model="capitaline_code"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">MVG Sector</span>
                            </label>
                            <input x-model="mvg_sector"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Agio Industry</span>
                            </label>
                            <input x-model="agio_indutry"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Remarks</span>
                            </label>
                            <input x-model="remarks"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">
                        {{-- <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Pan Number</span>
                            </label>
                            <input x-model="pan_number"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-96 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Email</span>
                            </label>
                            <input x-model="email"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Phone Number</span>
                            </label>
                            <input x-model="phone_number"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Whatsapp Number</span>
                            </label>
                            <input x-model="whatsapp"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div> --}}
                    </div>

                    <div class="w-full mt-10 mb-2 text-center">
                        <button type="submit" class="btn btn-primary" :disabled="!formValid() || mode == 'view'">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-base>