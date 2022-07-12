<div class="fixed z-20 top-0 left-0 h-full w-full justify-center bg-base-100 bg-opacity-70">
    <div class="w-96 bg-base-200 p-3 shadow-md mx-auto my-40">
        <form x-data="{password: '', confirm: ''}" action="" class="border border-base-100 rounded-md w-full p-4 relative">
            <button @click.prevent.stop="showform = false;" class="absolute top-1 right-1 z-20 btn btn-sm border border-base-100">
                <x-display.icon icon="icons.close" height="h-4" width="w-4" />
            </button>
            <h3 class="text-center my-2 p-1 font-bold text-lg">Change Password</h3>
            <div x-data="{showtext: false}" class="form-control w-full my-2">
                <label class="label">
                    <span class="label-text">New Password</span>
                </label>
                <div class="relative w-full p-0">
                    <input x-model="password" x-show="!showtext" type="password" placeholder="Type here" class="input input-bordered input-sm w-full pr-8" />
                    <input x-model="password" x-show="showtext" type="text" class="input input-bordered input-sm w-full pr-8" />
                    <button @click.prevent.stop="showtext = !showtext" class="absolute right-4 top-1 p-0 z-20 m-0 rounded-md opacity-60">
                        <x-display.icon x-show="showtext" icon="icons.view_on" height="h-4" width="w-4"/>
                        <x-display.icon x-show="!showtext" icon="icons.view_off" height="h-4" width="w-4"/>
                    </button>
                </div>
            </div>
            <div x-data="{showtext: false}" class="form-control w-full">
                <label class="label">
                    <span class="label-text">Confirm Password</span>
                </label>
                <div class="relative w-full p-0">
                    <input x-model="confirm" x-show="!showtext" type="password" placeholder="Type here" class="input input-bordered input-sm w-full" />
                    <input x-model="confirm" x-show="showtext" type="text" class="input input-bordered input-sm w-full" />
                    <button @click.prevent.stop="showtext = !showtext" class="absolute right-4 top-1 p-0 z-20 m-0 rounded-md opacity-60">
                        <x-display.icon x-show="showtext" icon="icons.view_on" height="h-4" width="w-4"/>
                        <x-display.icon x-show="!showtext" icon="icons.view_off" height="h-4" width="w-4"/>
                    </button>
                </div>
            </div>
            <div class="text-center mt-4 p-2">
                <button class="btn btn-sm">Submit</button>
            </div>
        </form>
    </div>
</div>
