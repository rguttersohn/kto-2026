<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <style>
        .password-generated-buttons-container{
            display:flex;

            button:first-child {
                border-radius: 0;

            }

            button:last-child {
                border-radius: 0 5px 0 0;
            }
        }

    </style>
    <div
        x-data="{ 
            state: $wire.$entangle(@js($getStatePath())),
            passwordGenerated: false,
            handlePasswordGeneration(){

                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
                let password = '';
                for(let i = 0; i < 16; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.state = password;
                this.passwordGenerated = true;

            },
            passwordCopied: false,
            handleCopyPassword(){

                const text = this.state ?? '';
                if (!text) return;

                const markCopied = () => {
                    this.passwordCopied = true;
                    setTimeout(() => { this.passwordCopied = false; }, 2000);
                };

                    navigator.clipboard.writeText(text)
                        .then(markCopied);
                
            },
            init(){

                this.$watch('state', (val)=>{

                    if(val === ''){

                        this.passwordGenerated = false;
                    }
                })
            },
            isShowingPassword: false
            }"
        {{ $getExtraAttributeBag() }}
    >
        <div class="fi-input-wrp fi-fo-text-input">
            <div class="fi-input-wrp-content-ctn">
                <input 
                    x-model="state"
                    class="fi-input" 
                    id="form.password" 
                    x-bind:type="isShowingPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    >
            </div>
            <template x-if="!passwordGenerated">
                <button
                    x-on:click.prevent="handlePasswordGeneration"
                    class="fi-color fi-color-success fi-bg-color-400 hover:fi-bg-color-300 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-900 hover:fi-text-color-800 dark:fi-text-color-950 dark:hover:fi-text-color-950 fi-btn fi-size-md  fi-ac-btn-action"
                    >
                    Generate Password
                </button>
            </template>
            <template x-if="passwordGenerated">
                <div class="password-generated-buttons-container">
                    <button
                        x-show="passwordGenerated"
                        x-on:click.prevent="handleCopyPassword"
                        class="fi-color fi-color-success fi-bg-color-400 hover:fi-bg-color-300 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-900 hover:fi-text-color-800 dark:fi-text-color-950 dark:hover:fi-text-color-950 fi-btn fi-size-md  fi-ac-btn-action"
                        >
                        <span x-show="!passwordCopied">Copy</span>
                        <span x-show="passwordCopied">Copied!</span>
                    </button>
                    <button 
                        x-on:click.prevent="isShowingPassword = !isShowingPassword"
                        class="fi-color fi-color-success fi-bg-color-400 hover:fi-bg-color-300 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-900 hover:fi-text-color-800 dark:fi-text-color-950 dark:hover:fi-text-color-950 fi-btn fi-size-md  fi-ac-btn-action"
                        >
                        <svg x-show="!isShowingPassword" class="fi-icon fi-size-lg" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="isShowingPassword" class="fi-icon fi-size-lg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

    </div>
</x-dynamic-component>
