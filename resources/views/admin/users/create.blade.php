<x-dashboard-base :ajax="$x_ajax">
    <div x-data="{ compact: $persist(false) }" class="p-3 border-b border-base-200 overflow-x-scroll">

        <div class="flex flex-col justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-left w-full">Add New User</h3>
            <div class="flex-grow flex flex-row justify-end items-center space-x-4">
                <form x-data="{
                    name: '',
                    username: '',
                    email: '',
                    password: '',
                    role: 0,
                    tl: 0,
                    result: 0,
                    error: '',
                    formValid() {
                        return this.name.length > 0 &&
                            isEmail(this.email) &&
                            this.role > 0
                    },
                    addUser() {
                        let params = {
                                name: this.name,
                                username: this.username,
                                email: this.email,
                                password: this.password,
                                role_id: this.role
                            }
                        if (this.role > 0 && this.tl > 0) {
                            params.tl_id = this.tl;
                        }
                        axios.post(
                            '{{route('users.store')}}',
                            params
                        ).then((r) => {
                            console.log(r);
                            if (r.data.success) {
                                this.name = '';
                                this.username = '';
                                this.email = '';
                                this.password = '';
                                this.role = 0;
                                this.tl = 0;
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
                        })
                        .catch((e) => {
                            this.result = -1;
                            this.error = 'Sorry, something wnet wrong.';
                            console.log(e);
                        });
                    }
                }" action="" @submit.prevent.stop="addUser();"
                class="p-4 border border-base-200 rounded-md w-96 relative">
                    <div x-show="result == 1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                            <div class="text-success">User added successfully!</div>
                            <div class="flex flex-row justify-evenly space-x-4">
                                <a href="#" @click.prevent.stop="result = 0; $dispatch('linkaction', {link: '{{route('users.index')}}'})" class="btn btn-sm capitalize">Back To All Users</a>
                                <button @click.prevent.stop="result = 0;" class="btn btn-sm capitalize">Add Another User</button>
                            </div>
                    </div>
                    <div x-show="result == -1" class="absolute top-0 left-0 z-20 w-full h-full bg-base-200 text-center flex flex-col space-y-8 items-center justify-center">
                        <div class="text-error">Couldn't add user:</div>
                        <div class="flex flex-col space-y-4 justify-center items-center space-x-4">
                            <span x-text="error"></span>
                            <button @click.prevent.stop="result = 0;" class="btn btn-sm capitalize">Ok</button>
                        </div>
                    </div>
                    <div class="form-control w-full max-w-md mb-1">
                        <label class="label mb-0 pb-0">
                          <span class="label-text">Name</span>
                        </label>
                        <input x-model="name" type="text" class="input input-bordered w-full max-w-md" required/>
                    </div>
                    <div class="form-control w-full max-w-md mb-1">
                        <label class="label mb-0 pb-0">
                          <span class="label-text">Username (Login)</span>
                        </label>
                        <input x-model="username" type="text" class="input input-bordered w-full max-w-md" required/>
                    </div>
                    <div class="form-control w-full max-w-md my-1">
                        <label class="label mb-0 pb-0">
                          <span class="label-text">Email</span>
                        </label>
                        <input x-model="email"  type="text" class="input input-bordered w-full max-w-md" required/>
                    </div>
                    <div class="form-control w-full max-w-md my-1">
                        <label class="label mb-0 pb-0">
                          <span class="label-text">Password</span>
                        </label>
                        <input x-model="password"  type="text" class="input input-bordered w-full max-w-md" minlength="8" required/>
                    </div>
                    <div class="form-control w-full max-w-md my-1">
                        <label class="label mb-0 pb-0">
                          <span class="label-text">Role</span>
                        </label>
                        <select x-model="role" class="select select-bordered">
                          <option value="0" disabled selected>Select</option>
                          @foreach ($roles as $role)
                             <option value="{{$role->id}}">{{$role->name}}</option>
                          @endforeach
                        </select>
                    </div>
                    <div x-show="role == {{$dlrid}}" class="form-control w-full max-w-md my-1">
                        <label class="label mb-0 pb-0">
                          <span class="label-text">Team Leader</span>
                        </label>
                        <select x-model="tl" class="select select-bordered">
                          <option value="0" disabled selected>Select</option>
                          @foreach ($tls as $tl)
                             <option value="{{$tl->id}}">{{$tl->name}}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-control w-full max-w-md mt-6 mb-2">
                        <button type="submit" class="btn btn-primary" :disabled="!formValid()">Add User</button>
                    </div>
                </form>
            </div>
            <div>
                <a href="#" @click="$dispatch('linkaction', {link: '{{route('users.index')}}'})" class="text-warning">Go Back</a>
            </div>
        </div>
    </div>
</x-dashboard-base>