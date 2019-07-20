layui.use(['form', 'layedit', 'laydate'], function() {
    var form = layui.form,
        layer = layui.layer
        //自定义验证规则

    form.verify({
        name: function(value) {
            if (value.length < 4) {
                return '昵称不能少于4个字符';
            }
            if (value.length > 16) {
                return '昵称不能多于16个字符';
            }

            if (escape(value).indexOf("%u") != -1) {
                return '昵称不能包含中文字符';
            }
        },
        pass: [
            /^[\S]{6,16}$/, '密码必须6到16位，且不能出现空格'
        ]
    });
    //监听提交
    form.on('submit(login)', function(data) {
        data = data.field;
        axios.post('/user💕dologin', {
                username: data.username,
                password: data.password,
            })
            .then(function(response) {
                if (response.data.code == 2000) { //上传成功
                    layer.msg(response.data.msg, { icon: 1, anim: 1 }, function() {
                        window.location.href = "/index💕index"
                    });
                } else {
                    layer.msg(response.data.msg, { icon: 5, anim: 6 });
                }
            });
        return false;
    });


});