<template>

    <GuestLayout :dir="dir()"

                 aria-expanded="false"
    >

        <Head :title="__('signin')"/>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="login" :value="__('phone')"/>

                <TextInput
                    id="login"
                    type="text"
                    classes="  "
                    v-model="form.login"
                    :error="form.errors.login"
                    required
                    autofocus
                    autocomplete="login">
                    <template v-slot:prepend>
                        <div class="p-3">
                            <UserIcon class="h-5 w-5"/>
                        </div>
                    </template>

                </TextInput>

            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="__('password')"/>

                <TextInput
                    id="password"
                    :type="showPassword?'text':'password'"
                    classes=" "
                    v-model="form.password"
                    required
                    :error="form.errors.password"
                    autocomplete="password">

                    <template v-slot:prepend>
                        <div class="p-3" @click="showPassword=!showPassword">
                            <EyeIcon v-if="!showPassword"
                                     class="h-5 w-5   "/>
                            <EyeSlashIcon v-else class="h-5 w-5 "/>
                        </div>
                    </template>
                </TextInput>

            </div>

            <div class="flex mt-4  items-center  justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember"/>
                    <span class="m-2 text-sm text-gray-600">{{ __('remember_me') }}</span>
                </label>

            </div>
            <SecondaryButton type="button" ref="forget" :label="__('forgot_my_password')" class="text-sm"
                             @click=" $refs.forget.setLoading(true)  ; tgSendSMS(form.login,'forget').then((res)=>{
                                           $refs.forget.setLoading(false) ;
                                       }) "
            >

            </SecondaryButton>
            <div class=" relative   mt-4">

                <PrimaryButton :loading="form.processing" :label="__('signin')" class="w-full   "
                               :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                </PrimaryButton>

            </div>
            <div v-if=" route().current('tma.login-form')" class="w-full mt-5">
                <span>{{ __('not_have_account?') }}</span>
                <Link
                    @click="$inertia.visit(route('tma.register-form'))"
                    href=" "

                    class="underline mx-2 text-sm   link_color rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    {{ __('signup') }}
                </Link>
            </div>

        </form>
    </GuestLayout>
</template>
<script>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/24/outline";
import WebApp from "@twa-dev/sdk";
import mitt from 'mitt';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const emitter = mitt();
export default {

    data() {
        return {
            form: useForm({
                login: '',
                password: '',
                remember: false,
                telegram_id: false,

            }),
            showPassword: false,
        }
    },
    props: {
        canResetPassword: Boolean,
        status: String,

    }, components: {
        SecondaryButton,
        GuestLayout,
        PrimaryButton,
        InputLabel,
        InputError,
        Checkbox,
        TextInput,
        UserIcon,
        EyeIcon,
        EyeSlashIcon,
        Head,
        Link,
    },

    methods: {
        submit() {

            this.form.clearErrors();
            this.form.telegram_id = (this.tgUser || {}).id;

            this.form.processing = true;
            this.toServer(route('tma.login', {
                    login: this.form.login,
                    password: this.form.password,
                    telegram_id: this.form.telegram_id
                }), 'post',
                {
                    params: {},
                    callback: (type, res) => {
                        this.form.processing = false;
                        // this.log(type);
                        // this.log(res);
                        if (type === 'success') {
                            if (res.user) {
                                // this.$inertia.visit()
                                setTimeout(function () {
                                    // emitter.emit('updateUser', res.user);
                                    emitter.emit('navbar_loading', true);
                                    // location.reload();
                                    // this.updateUser(res.user);
                                }, 1000)

                                this.tgBack();
                                // location.reload();
                                // this.$page.props.auth.user = res.user;
                            }
                        } else {
                            this.form.errors = this.parseErrors(res.errors);
                            // this.log(res);
                        }
                    }
                });
        }
    }
}


</script>
