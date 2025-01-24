import {usePage,} from "@inertiajs/vue3";

import WebApp from "@twa-dev/sdk";

let self;
export default {

    // emits: ['showToast'],
    // setup(props, ctx) {
    //     ctx.emit('showToast')
    // },

    data() {
        return {
            user: null,
            tgInitData: null,
            tgUser: {id: null, first_name: null, last_name: null, language_code: "en", allows_write_to_pm: false},
            TGW: null,
            userData: {user: this.tgUser, auth_date: null, hash: null, query_id: null,},
        }
    },

    mounted() {
        self = this;
        // console.log(inject('toast'));
        if (usePage().props.auth)
            this.user = usePage().props.auth.user;

        window.axios = axios.create();
        window.axios.interceptors.response.use(undefined, function (error) {
                error.handleGlobally = (error) => {
                    return () => {
                        const statusCode = error.response ? error.response.status : null;
                        if (statusCode === 419) {
                            location.reload();
                        }

                    }
                }

                return Promise.reject(error);
            }
        );
    },

    methods: {
        tgBack() {
            window.history.back();
        },
        tgTelegramId() {
            return this.tgUser.id;
        },
        tgInit() {

            WebApp.ready();

            this.WebApp = WebApp;
            window.TGW = WebApp;
            WebApp.BackButton.show();
            WebApp.BackButton.onClick(this.tgBack);

            this.tgVerifyInitData(WebApp.initData).then((res) => {
                // alert(WebApp.initData);
                // console.log(res)
                if (res === true)
                    this.tgSetInitData();
            }).catch((res) => {
                console.log(res);
            });
        },
        tgSetInitData() {
            WebApp.ready();
            this.tgInitData = new URLSearchParams(WebApp.initData);
            this.userData = Object.fromEntries(this.tgInitData);
            this.tgUser = (this.userData || {}).user;
            // console.log(this.userData);
            // this.tgShowAlert(this.tgUser);
            let tg = this.base64encode(this.tgInitData);
            // const startParam = WebApp.initDataUnsafe.start_param;
            document.cookie = "tg_init_data=" + tg;
            // WebApp.setHeaderColor('bg_color');

        }
        ,
        tgOpenLink(link) {
            return new Promise(function (resolve, reject) {

                var patternLink = /^((http|https|ftp):\/\/)/;
                var patternRoute = /^((tma.))/;
                if (patternRoute.test(link)) {
                    self.$inertia.visit(route(link));
                    resolve(true);
                } else if (patternLink.test(link)) {
                    WebApp.openLink(link);
                    resolve(true);
                } else self.toServer(route('tma.manage', {cmnd: link}), 'get',
                    {
                        params: {},
                        callback: (type, res) => {
                            resolve(true);
                            if (res.url) {
                                WebApp.openLink(res.url);
                            } else if (res.message) {
                                this.tgShowAlert(res.message);
                            } else if (res.result == 'end_sub') {
                                self.tgShowPopup(self.__('end_sub'), self.__('please_buy_sub'),
                                    [
                                        // {id: 'delete', type: 'destructive', text: 'Delete all'},
                                        {id: 'tma.shop', type: 'default', text: self.__('buy_sub')},
                                        {type: 'destructive', text: self.__('cancel')},
                                    ],
                                    (buttonId) => {
                                        if (buttonId === 'tma.shop') {

                                            self.$inertia.visit(route('tma.shop'));
                                        }
                                    });
                            }
                        }
                    });
            });
        },

        tgShowAlert(str) {

            // if (WebApp.version > 6.2)
            // alert(str);
            try {
                WebApp.showAlert(str);
            } catch (e) {
                alert(str);
            }
        },
        tgShowPopup(title, message, buttons, callback) {

            try {
                WebApp.showPopup({
                    title: title,
                    message: message,
                    buttons: buttons,
                }, callback);
            } catch (e) {
                alert(message);
            }
        },

        tgGetInitData() {
            WebApp.ready();
            this.tgInitData = new URLSearchParams(WebApp.initData);
            return this.tgInitData;


        }
        ,
        base64encode(str) {
            let encode = encodeURIComponent(str).replace(/%([a-f0-9]{2})/gi, (m, $1) => String.fromCharCode(parseInt($1, 16)))
            return btoa(encode)
        }
        ,
        tgVerifyInitData(initData) {
            this.emitter.emit('navbar_loading', true);
            return new Promise(function (resolve, reject) {
                self.toServer(route('tma.validation'), 'post',
                    {
                        params: {init_data: initData},
                        callback: (type, res) => {
                            self.emitter.emit('navbar_loading', false);

                            if (res.user) {
                                self.updateUser(res.user);
                            }
                            if (type === 'success') {
                                resolve(true)
                            } else reject(false)
                        }
                    });
            });
        },
        tgSendSMS(phone, type = 'verification') {
            return new Promise(function (resolve, reject) {
                self.toServer(route('tma.sms'), 'post',
                    {
                        params: {phone: phone, type: type},
                        callback: (type, res) => {

                            resolve(true)
                            if (res.message)
                                self.tgShowAlert(res.message);

                        }
                    });
            });
        },
        tgLogout() {
            this.emitter.emit('navbar_loading', true);
            let tmp = this.$page.props.auth.user;
            this.$page.props.auth.user = null;
            this.toServer(route('tma.logout'), 'post',
                {
                    params: {},
                    callback: (type, res) => {
                        this.emitter.emit('navbar_loading', false);
                        if (type === 'success') {
                            // location.reload();
                        } else {
                            this.$page.props.auth.user = tmp;
                        }
                    }
                });
        },

        // tgVerifyWebAppData(initData) {
        //     // It is not clear from the documentation weather is URL
        //     // escaped or not, maybe you will need to uncomment this
        //     // initData = decodeURIComponent(initData)
        //     // Parse URL Query
        //     const q = new URLSearchParams(initData);
        //     // Extract the hash
        //     const hash = q.get("hash");
        //
        //     // Re encode in accordance to the documentation. Remember
        //     // to remove hash before.
        //     q.delete("hash");
        //     const v = Array.from(q.entries());
        //     v.sort(([aN, aV], [bN, bV]) => aN.localeCompare(bN));
        //     const data_chack_string = v.map(([n, v]) => `${n}=${v}`).join("\n");
        //
        //     // Perform the algorithm provided with the documentation
        //     var secret_key = HmacSHA256(import.meta.env.VITE_TELEGRAM_BOT_TOKEN, "WebAppData").toString(Hex);
        //     var key = HmacSHA256(data_chack_string, secret_key).toString(Hex);
        //
        //     return key === hash;
        // },
        updateUser(user) {
            usePage().props.auth.user = user;
            this.emitter.emit('updateUser', user);

        }
        ,
        toServer(url, method = 'get', {
            callback = () => {
            }, params = {}, headers = {}
        },) {
            // this.emitter.emit('loading', true);

            window.axios({
                method: method,
                url: url,
                headers: headers,
                params: params,
                data: params,
                onUploadProgress: function (progressEvent) {
                    // Do whatever you want with the native progress event
                },
                onDownloadProgress: function (progressEvent) {
                    // Do whatever you want with the native progress event
                },
                // responseType: 'stream'
            })
                .then(function (response) {

                    callback('success', response.data);
                })
                .catch(function (error) {
                    return;
                    if (error.response) {
                        // The request was made and the server responded with a status code
                        // that falls out of the range of 2xx
                        // console.log(error.response.data);
                        // console.log(error.response.status);
                        // console.log(error.response.headers);
                        if (error.response.status == 401)
                            self.$inertia.visit(route('tma.login-form'));
                        else
                            callback('error', error.response.data || error.response);
                    } else if (error.request) {
                        // The request was made but no response was received
                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                        // http.ClientRequest in node.js
                        // console.log(error.request);
                        callback('error', error.request);
                    } else {
                        // Something happened in setting up the request that triggered an Error
                        // console.log('Error', error.message);
                        callback('error', error.message);
                    }
                })
                .finally(function () {
                    // self.emitter.emit('loading', false);
                    // always executed
                });

        }
        ,

        log(str) {
            console.log(str);
        }
    }
}
