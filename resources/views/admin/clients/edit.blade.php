<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{ compact: $persist(false), mode: 'view' }" class="p-3 overflow-x-scroll">

        <div class="flex flex-col justify-between items-center mb-4">
            <div class="flex flex-row justify-between w-full pr-10 items-center">
                <div class="flex flex-row items-center mb-8">
                    <h3 class="text-xl font-bold text-left">Edit Client</h3>
                    <button @click.prevent.stop="mode == 'view' ? mode = 'edit' : mode = 'view';" class="btn btn-ghost" :class="mode == 'view' ? 'text-warning' : 'text-accent'">
                        <x-display.icon icon="icons.edit" height="h-6" width="w-6"/>
                    </button>
                </div>
                <a href="#" @click="$dispatch('linkaction', {link: '{{route('clients.show', $client->id)}}', route: 'clients.show'})" class="text-warning">Go Back</a>
            </div>

            <div>

            </div>
            <div class="flex-grow flex flex-row justify-end items-center space-x-4 w-full">
                <form x-data="{
                    name: '',
                    client_code: '',
                    unique_code: '',
                    dealer: '',
                    fresh_fund: 0,
                    re_invest: 0,
                    withdrawal: 0,
                    payout: 0,
                    total_aum: 0,
                    other_funds: 0,
                    brokerage: 0,
                    realised_pnl: 0,
                    ledger_balance: 0,
                    pfo_type: 0,
                    category: 0,
                    type: 0,
                    fno: 0,
                    pan_number: '',
                    email: '',
                    phone_number: '',
                    whatsapp: '',
                    result: 0,
                    error: '',
                    formValid() {
                        return this.name.length > 0 &&
                            isEmail(this.email);
                    },
                    updateClient() {
                        let params = {
                                _method: 'PUT',
                                name: this.name,
                                client_code: this.client_code,
                                unique_code: this.unique_code,
                                rm_id: this.dealer,
                                fresh_fund: this.fresh_fund,
                                re_invest: this.re_invest,
                                withdrawal: this.withdrawal,
                                payout: this.payout,
                                total_aum: this.total_aum,
                                other_funds: this.other_funds,
                                brokerage: this.brokerage,
                                realised_pnl: this.realised_pnl,
                                ledger_balance: this.ledger_balance,
                                pfo_type: this.pfo_type,
                                category: this.category,
                                type: this.type,
                                fno: this.fno,
                                pan_number: this.pan_number,
                                email: this.email,
                                phone_number: this.phone_number,
                                whatsapp: this.whatsapp,
                            };

                        axios.post(
                            '{{route('clients.update', $client->id)}}',
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
                    name = '{{$client->name}}';
                    client_code = '{{$client->client_code}}';
                    unique_code = '{{$client->unique_code}}';
                    dealer = '{{$client->rm_id}}';
                    fresh_fund = '{{$client->fresh_fund}}';
                    re_invest = '{{$client->re_invest}}';
                    withdrawal = '{{$client->withdrawal}}';
                    payout = '{{$client->payout}}';
                    total_aum = '{{$client->total_aum}}';
                    other_funds = '{{$client->other_funds}}';
                    brokerage = '{{$client->brokerage}}';
                    realised_pnl = '{{$client->realised_pnl}}';
                    ledger_balance = '{{$client->ledger_balance}}';
                    pfo_type = '{{$client->pfo_type}}';
                    category = '{{$client->category}}';
                    type = '{{$client->type}}';
                    fno = '{{$client->fno}}';
                    pan_number = '{{$client->pan_number}}';
                    email = '{{$client->email}}';
                    phone_number = '{{$client->phone_number}}';
                    whatsapp = '{{$client->whatsapp}}';
                "
                action="" @submit.prevent.stop="updateClient();"
                class="p-4 border border-base-200 rounded-md w-full relative">
                    <div x-show="result == 1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                            <div class="text-success">Client added successfully!</div>
                            <div class="flex flex-row justify-evenly space-x-4">
                                <a href="#" @click.prevent.stop="result = 0; $dispatch('linkaction', {link: '{{route('clients.show', $client->id)}}', route: 'clients.show', fresh: true})" class="btn btn-sm capitalize">Ok</a>
                            </div>
                    </div>
                    <div x-show="result == -1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                        <div class="text-error">Couldn't update the client:</div>
                        <div class="flex flex-col space-y-4 justify-center items-center space-x-4">
                            <span x-text="error"></span>
                            <button @click.prevent.stop="result = 0;" class="btn btn-sm btn-error capitalize">Ok</button>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start mb-3">
                        <div class="form-control w-96">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Name</span>
                            </label>
                            <input x-model="name" type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" required :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-52">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Client Code</span>
                            </label>
                            <input x-model="client_code" type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" required :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-52 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Unique Code</span>
                            </label>
                            <input x-model="unique_code"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" required :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-52 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Dealer</span>
                            </label>
                            <select x-model="dealer" class="select select-bordered" :disabled="mode == 'view'">
                            <option value="0" disabled selected>Dealer</option>
                            @foreach ($dealers as $dealer)
                                <option value="{{$dealer->id}}">{{$dealer->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Total AUM</span>
                            </label>
                            <input x-model="total_aum"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Fresh Fund</span>
                            </label>
                            <input x-model="fresh_fund"  type="text" class="w-48 m-0 input input-bordered read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Re-invest</span>
                            </label>
                            <input x-model="re_invest"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Withdrawal</span>
                            </label>
                            <input x-model="withdrawal"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Payout</span>
                            </label>
                            <input x-model="payout"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Other Funds</span>
                            </label>
                            <input x-model="other_funds"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Brokerage</span>
                            </label>
                            <input x-model="brokerage"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Realised PNL</span>
                            </label>
                            <input x-model="realised_pnl"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                        <div class="form-control w-48 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Ledger Balance</span>
                            </label>
                            <input x-model="ledger_balance"  type="text" class="input input-bordered w-full max-w-md read-only:bg-base-200" :readonly="mode == 'view'"/>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">
                        <div class="form-control w-64 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">PFO Type</span>
                            </label>
                            <select x-model="pfo_type" class="select select-bordered" :disabled="mode == 'view'">
                            <option value="0" disabled selected>Select PFO Type</option>
                            @foreach ($pfo_types as $type)
                                <option value="{{$type}}">{{$type}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-control w-64 max-w-md my-1">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Category</span>
                            </label>
                            <select x-model="category" class="select select-bordered" :disabled="mode == 'view'">
                            <option value="0" disabled selected>Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{$category}}">{{$category}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-control w-64 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">Type</span>
                            </label>
                            <select x-model="type" class="select select-bordered" :disabled="mode == 'view'">
                            <option value="0" disabled selected>Select</option>
                            @foreach ($types as $type)
                                <option value="{{$type}}">{{$type}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-control w-64 max-w-md">
                            <label class="label mb-0 pb-0">
                            <span class="label-text">FNO</span>
                            </label>
                            <select x-model="fno" class="select select-bordered" :disabled="mode == 'view'">
                                <option value="0" disabled selected>Select</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-row space-x-8 flex-wrap justify-start items-start my-3">
                        <div class="form-control w-48 max-w-md">
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
                        </div>
                    </div>

                    <div class="w-full mt-10 mb-2 text-center">
                        <button type="submit" class="btn btn-primary" :disabled="!formValid() || mode == 'view'">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-base>