
{{-- 继承 layouts.app 模板 --}}
@extends('layouts.app')

    @section('title','dozhan-从这里开始!')

    {{--填充 meta 布局--}}
    @section('meta')
        <style>
            .log-container {
                width: 350px;
                height: 530px;
                margin: 30px auto 200px auto;
                overflow: hidden;
                position: relative;
            }

            .log-logo {
                width: 200px;
                margin: 0 auto;
                padding: 30px 0;
            }

            .log-logo img {
                width: 100%;
            }

            .log-input {
                text-align: center;
                position: relative;
            }

            .log-ct {
                margin-bottom: 300px;
            }

            .lin-input {
                text-align: center;
            }

            .lin-input input {
                width: 300px;
                outline: none;
                font-size: 14px;
                border: 1px solid #d2d2d2;
                padding: 10px 7px;
                border-radius: 3px;
                color: #34495e;
                letter-spacing: 1px;
                margin-bottom: 10px;
                transition: border .2s;
            }

            .log-input input {
                width: 300px;
                outline: none;
                font-size: 14px;
                border: 1px solid #d2d2d2;
                padding: 10px 7px;
                border-radius: 3px;
                color: #34495e;
                letter-spacing: 1px;
                margin-bottom: 10px;
                transition: border .2s;
            }

            .log-input input:disabled {
                background: #f7f7f7;
            }

            .log-input input:focus {
                transition: border .2s;
                border: 1px solid #34495e;
            }

            .log-input input::placeholder {
                font-weight: 200;
            }

            .log-input .i-user {

            }

            .log-input .log-btn {
                background: #3e8ee2;
                color: #fff;
                outline: none;
                padding: 10px;
                width: 315px;
                cursor: pointer;
                font-weight: 200;
                letter-spacing: 3px;
                border-radius: 3px;
                border: none;
                margin: 30px auto 30px auto;
            }

            .result-tips {
                font-size: 14px;
                text-align: left;
                padding-left: 20px;
                position: relative;
                list-style: none;
                margin: 0;
            }

            .log-input .log-btn:disabled {
                background: #89bff7;
            }

            .log-input .lin-btn {
                outline: none;
                color: #3e8ee2;
                padding: 10px;
                background: #fff;
                border: 1px solid #3e8ee2;
                width: 315px;
                cursor: pointer;
                font-weight: 200;
                letter-spacing: 3px;
                border-radius: 3px;
            }

            .log-input .log-lp {
                font-size: 14px;
                font-weight: 400;
                color: #528edb;
                width: 70px;
                letter-spacing: 2px;
                cursor: pointer;
                margin: 0 auto;
            }

            .log-input .log-or-c {
                width: 315px;
                border-top: 1px solid #e2e2e2;
                margin: 40px auto 20px auto;
            }

            .log-input .log-or-title {
                color: #9c9c9c;
                font-weight: 200;
                font-size: 14px;
                position: relative;
                top: -10px;
                background: #fff;
                width: 32px;
                margin: 0 auto;
                padding: 0 10px;
                letter-spacing: 2px;
                cursor: default;
            }

            .log-input .lin-or {
                margin: 0px auto 20px auto;
            }

            .log-status-page {
                width: 100%;
                height: 100%;
                background: #fff;
                position: absolute;
                z-index: 99;
                padding-top: 120px;
            }

            .log-status-title {
                color: #38495c;
                font-size: 14px;
            }

            .log-status-page img {
                width: 100px;
            }

            .log-status-active {
                font-size: 12px;
                color: #b2b1b1;
                padding: 30px 0;
                cursor: default;
                letter-spacing: 2px;
            }

            .log-status-thumb {
                position: absolute;
                width: 100%;
                height: 100%;
                background: #fffffff2;
                z-index: 9;
                text-align: center;
                vertical-align: middle;
                display: table;
            }

            .log-status-thumb .log-status-icon-group-wait {
                display: table-cell;
                text-align: center;
                padding-top: 100px;

            }

            .log-status-thumb .log-status-icon-group-success {
                position: absolute;
                top: 160px;
                right: 120px;

            }

            .log-status-icon {
                width: 100px;
            }

            .log-status-icon-w {
                width: 70px;
            }

            .log-status-icon-title {
                text-align: center;
            }

            .log-status-icon-group-wait .success {
                width: 100px;
            }

            .log-status-icon-group-success {
                text-align: center;
            }

            .log-status-icon-group-warning {
                position: absolute;
                top: 60px;
                right: 115px;
                text-align: center;
            }

            .input-verify-group input {
                width: 170px;
                margin-right: -6px;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
                letter-spacing: 2px;
                margin-top: 30px;
            }

            .input-verify-group input:focus {
                border: 1px solid #c8c8c8;
            }

            .input-verify-group button {
                outline: none;
                cursor: pointer;
                letter-spacing: 2px;
                font-weight: 200;

                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
                border-top-right-radius: 3px;
                border-bottom-right-radius: 3px;

                background: #2196F3;
                color: #fff;
                border: none;
                font-size: 14px;
                padding: 11px 20px;
            }

            .input-verify-group button:disabled {
                background: #97a8b1;
                cursor: default;
            }
            .error-tips{
                color: #446682;
                padding: 10px 0;
            }
            .vcode-message{
                padding: 10px 30px;
                font-size: 14px;
                text-align: left;
            }
        </style>
    @endsection

    {{-- 填充 conntent 布局 --}}
    @section('content')
        <!--核心-->
        <div class="log-container" id="logapp">

            <transition name="fade">

                <div class="log-status-thumb"  v-if="tips_info.show_waite">

                    <transition name="fade">
                        <div class="log-status-icon-group-wait"  v-if="tips_info.show_waite">

                            <img class="log-status-icon-w" src="{{ asset('img/icon/progress-bar-preloader.svg') }}">

                        </div>


                    </transition>

                </div>
            </transition>

            <transition name="fade">

                <div class="log-status-thumb" v-if="tips_info.show_error!=''">

                    <transition name="fade">
                        <div class="log-status-icon-group-wait" v-if="tips_info.show_error!=''">

                            <img class="log-status-icon-w" src="{{ asset('img/icon/error.svg') }}">
                            <div class="error-tips">@{{tips_info.show_error}}</div>

                        </div>


                    </transition>

                </div>
            </transition>

            <transition name="fade">
                <div v-if="log" class="log-ct">


                    <div class="log-input">


                        <div class="log-logo">
                            <img src="{{ asset('img/logos/log-log.svg') }}">
                        </div>


                        <div class="log-input">

                            <input placeholder="邮箱地址" type="text" class="i-user" title="username">
                            <input placeholder="密码" type="password" class="i-pass" title="password">

                            <transition name="fade">
                                <div v-if="tips_info.status_context!=''" class="result-tips text-danger">
                                    @{{tips_info.status_context}}
                                </div>
                            </transition>

                            <button class="log-btn" @click="tips_info.show_waite=true;rand()">登陆</button>

                            <div class="log-lp">忘记密码</div>

                            <div class="log-or-c">
                                <div class="log-or-title">或者</div>
                            </div>

                            <button class="lin-btn" @click="log=!log;">注册</button>

                        </div>
                    </div>

                </div>
            </transition>

            <transition name="fade">
                <div v-if="!log">


                    <div class="log-input">

                        <transition name="fade">
                            <div class="log-status-page" v-if="sendemail">


                                <transition name="fade">
                                    <div class="log-status-thumb" style="margin-top: -100px;" v-if="verify_info.show_vcode_status!=0">


                                        <div class="log-status-icon-group-wait" v-if="verify_info.show_vcode_status!=0">

                                            <img class="log-status-icon-w" src="{{ asset('img/icon/article_loading.svg') }}" v-if="verify_info.show_vcode_status==1">
                                            <img class="log-status-icon-w" src="{{ asset('img/icon/success.svg') }}" v-if="verify_info.show_vcode_status==2">

                                        </div>


                                    </div>

                                </transition>





                                <!--<img src="img/icon/sendmail.svg">-->
                                <div class="log-status-title">已向您的邮箱发送一封验证邮件，请注意查收!</div>

                                <div class="input-verify-group">
                                    <input @input="verify_code()" v-model="conf_v_code" title="验证码" placeholder="验证码">
                                    <button  :disabled="tips_info.conf_v" id="conf_verify" @click="verify_logup">确认验证</button>
                                </div>
                                <div class="text-danger vcode-message" v-if="verify_info.show_vcode_status==0">@{{verify_info.re_context}}</div>
                                <div class="log-status-active">
                                    没收到邮件？| <span class="active-link" v-if="resend==0" @click="logup_btn">重新发送</span>
                                    <span v-if="resend>0">@{{resend}}秒后重新发送</span>
                                </div>
                            </div>
                        </transition>

                        <div class="log-logo">
                            <img src="{{ asset('img/logos/log-log.svg') }}">
                        </div>

                        <input @input="check_email" spellcheck="false" autocomplete="off" v-model="logup_input.input_email"
                               id="logup-user"
                               placeholder="邮箱地址" type="text" class="i-user" title="username">

                        <input @input="check_password" spellcheck="false" autocomplete="off"
                               v-model="logup_input.input_password" id="logup-pass"
                               placeholder="密码" type="password"
                               class="i-pass" title="password">

                        <input @input="check_repassword" spellcheck="false" autocomplete="off"
                               v-model="logup_input.input_repassword" id="logup-repass"
                               placeholder="确认密码" type="password"
                               class="i-pass" title="password">


                        <transition name="fade">
                            <ul class="result-tips text-danger">

                                <li v-if="value!=null" v-for="(value,key) in logup_input.error">
                                    @{{value}}
                                </li>
                            </ul>
                        </transition>


                        <button class="log-btn" @click="logup_btn();">注册</button>

                        <div class="log-or-c lin-or">
                            <div class="log-or-title lin-or-title">或者</div>
                        </div>

                        <button class="lin-btn" @click="log=!log">登陆</button>
                    </div>
                </div>
            </transition>
        </div>
    @endsection

    {{-- 填充 footer 布局 --}}
    @section('footer')
        <!--验证-->
        <script type="text/javascript">

            Vue.use(VeeValidate);

            var log_app = new Vue({
                el: '#logapp',
                data: {
                    log: true,
                    sendemail: false,
                    resend: 60,
                    resend_timer: '',
                    request_server: 'https://www.hjn.ink/api/',


                    conf_v_code:null,



                    verify_info:{
                        show_vcode_status:0,
                        re_context: '',
                        email_key:null
                    },

                    tips_info: {
                        show_waite: false,
                        show_error:'',
                        conf_v:true,
                        status: 'success',
                        status_context: ''
                    },

                    logup_input: {
                        input_email: null,
                        input_password: null,
                        input_repassword: null,
                        error: {
                            email: null,
                            password: null,
                            rpassword: null,
                        }
                    }


                },
                methods: {
                    check_email: function () {

                        var email_fil = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
                        if (email_fil.test(this.logup_input.input_email)) {

                            this.logup_input.error.email = null
                        } else {
                            this.logup_input.error.email = '邮箱格式有误，请重新输入'
                        }
                    },

                    check_password: function () {


                        var pass_fil = /(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/;
                        if (pass_fil.test(this.logup_input.input_password)) {
                            this.logup_input.error.password = null
                        } else {
                            this.logup_input.error.password = '密码必须字母+数字,最少6位'
                        }


                        if (this.logup_input.input_repassword) {
                            if (this.logup_input.input_password == this.logup_input.input_repassword) {
                                this.logup_input.error.rpassword = null

                            } else {
                                this.logup_input.error.rpassword = '两次密码不一致'

                            }
                        }


                    },


                    check_repassword: function () {


                        if (this.logup_input.input_password == this.logup_input.input_repassword) {
                            this.logup_input.error.rpassword = null

                        } else {
                            this.logup_input.error.rpassword = '两次密码不一致'

                        }

                    },
                    verify_logup:function(){

                        var pb = this;

                        pb.verify_info.show_vcode_status = 1;


                        Vue.http.post(this.request_server + 'user',
                            {
                                email: this.logup_input.input_email,
                                password: this.logup_input.input_password,
                                key: this.verify_info.email_key,
                                code: this.conf_v_code
                            },
                            {emulateJSON: true}
                        ).then(function (response) {



                            pb.verify_info.show_vcode_status = 2;

                            setTimeout(function () {
                                pb.sendemail = false;
                                pb.log = true;
                                pb.tips_info.show_waite = false;
                                pb.verify_info.show_vcode_status = 0;
                                pb.verify_info.re_context = '';

                            },2000)




                        }).catch(function (response) {



                            setTimeout(function () {

                                pb.verify_info.show_vcode_status = 0;
                                pb.verify_info.re_context = response.body.message;

                            },1000)


                        })



                    },
                    verify_code:function(){
                        if (this.conf_v_code.length>=4){
                            this.tips_info.conf_v = false
                        }else{
                            this.tips_info.conf_v = true

                        }
                    },

                    logup_btn: function () {

                        var pb = this;
                        var ky = pb.logup_input;
                        var ke = pb.logup_input.error;

                        if (ky.input_email!=null && ky.input_password!=null && ky.input_repassword!=null){

                            if(ke.email==null && ke.password==null && ke.rpassword==null){


                                pb.tips_info.show_waite = true;


                                Vue.http.post(this.request_server + 'verificationCodes/email',
                                    {
                                        email: pb.logup_input.input_email,
                                        password: pb.logup_input.input_password
                                    },
                                    {emulateJSON: true}
                                ).then(function (response) {

                                    if(response.status==200){
                                        pb.sendemail = true;
                                        pb.countemail();
                                        pb.verify_info.email_key = response.body.key;
                                    }

                                })
                                    .catch(function (response) {
                                        pb.tips_info.show_waite = false;
                                        pb.tips_info.show_error = response.body.message;

                                        setTimeout(function () {
                                            pb.tips_info.show_error = '';

                                        },2000)


                                    })


                            }





                        }








                    },


                    countemail: function () {

                        var rapp = this;
                        rapp.resend = 60;

                        this.resend_timer = setInterval(function () {
                            if (rapp.resend != 0) {
                                rapp.resend--;
                            }
                            if (rapp.resend == 0) {

                                clearInterval(rapp.resend_timer);
                                rapp.resend_timer = null;
                            }

                        }, 1000)
                    }
                },
            });

        </script>
    @endsection
