layui.use(['form', 'layedit', 'laydate'], function() {
    var form = layui.form,
        layer = layui.layer
        //自定义验证规则

    form.verify({
        qq: function(value) {
            if (value.length < 5) {
                return 'QQ号最短是5位哦◉‿◉ ';
            }
            if(!/^[0-9]*$/.test(value)){
                return 'QQ号是数字呢◉‿◉ ';
            }
        },
        google_auth: function(value) {
            if (value.length !=6) {
                return 'google验证码是6位哦( •́ .̫ •̀ )';
            }
        },
        pass: [
            /^[\S]{6,16}$/, '密码是6到16位，且不能出现空格ʕ ᵔᴥᵔ ʔ'
        ],
    });
    //监听提交
    form.on('submit(register)', function(data) {
        data = data.field;
        axios.post('/user💕doregister', {
                qq: data.qq,
                password: data.password,
                google_auth:data.google_auth,
                code: data.code,
                secret:data.secret
            })
            .then(function(response) {
                if (response.data.code == 200) {
                    layer.msg(response.data.msg + '要牢记你的用户名和密码哦', { icon: 1, anim: 1 }, function() {
                        window.location.href = "/user💕login"
                    });
                } else {
                    layer.msg(response.data.msg, { icon: 5, anim: 6 });
                }
            });
        return false;
    });


});