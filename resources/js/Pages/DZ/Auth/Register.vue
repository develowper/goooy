<template>

    <GuestLayout :dir="dir()"

                 aria-expanded="false"
    >

        <Head :title="__('register')"/>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>

                <PhoneFields
                    v-model:phone="form.phone"
                    v-model:phone-verify="form.code"
                    :phone-error="form.errors.phone"
                    :phone-verify-error="form.errors.code"
                    type="verification"
                    for="users"
                    :verified="null"
                    :activeButtonText="__('send_code')"
                    :disable="null"
                    :disableEdit="null"
                />

            </div>
            <div class="my-2">
                <InputLabel for="fullname" :value="__('fullname')"/>

                <TextInput
                    id="fullname"
                    type="fullname"
                    classes="  "
                    v-model="form.fullname"
                    :error="form.errors.fullname"
                    required
                    autofocus
                >
                    <template v-slot:prepend>
                        <div class="p-3">
                            <UserIcon class="h-5 w-5"/>
                        </div>
                    </template>

                </TextInput>

            </div>


            <div class="mt-2">
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
            <div class="my-2">
                <InputLabel for="marketer_code" :value="`${__('marketer_code')} (${__('optional')})`"/>

                <TextInput
                    id="marketer_code"
                    type="marketer_code"
                    classes="  "
                    v-model="form.marketer_code"
                    :error="form.errors.marketer_code"
                    required
                    autofocus
                >
                    <template v-slot:prepend>
                        <div class="p-3">
                            <UserIcon class="h-5 w-5"/>
                        </div>
                    </template>

                </TextInput>

            </div>
            <div class="flex mt-4  items-center  justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.is_lawyer"/>
                    <span class="m-2 text-sm text-gray-600">{{ __('i_am_lawyer_expert') }}</span>
                </label>

            </div>

            <div class=" relative   mt-4">

                <PrimaryButton :loading="form.processing" :label="__('register')" class="w-full   "
                               :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                </PrimaryButton>

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
import PhoneFields from "../../../Components/PhoneFields.vue";

const emitter = mitt();
export default {

    data() {
        return {
            form: useForm({
                fullname: null,
                phone: null,
                code: null,
                is_lawyer: null,
                marketer_code: null,
                telegram_id: null,

            }),
            showPassword: false,
        }
    },
    props: {
        canResetPassword: Boolean,
        status: String,

    }, components: {
        PhoneFields,
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
            this.toServer(route('tma.register', {
                    fullname: this.form.fullname,
                    phone: this.form.phone,
                    password: this.form.password,
                    code: this.form.code,
                    is_lawyer: this.form.is_lawyer,
                    marketer_code: this.form.marketer_code,
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

                                this.$inertia.visit(route('tma.index'));
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
