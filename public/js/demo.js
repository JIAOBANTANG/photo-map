function openVideoPage() {
    var entity = {};// 传输数据的实体变量
    var flag = false;// 标识，用来判断是否选择文件，选择的文件大小是否大于零
    var coversflag = false;// 这个可能没用，但是不想删除。
    layer.open({// 将要打开一个弹出层
        type: 1,
        title: "基本信息",
        content: ugcUploadPage.value,
        closeBtn: 2,
        area: ["700px", "600px"],
        shift: 5,
        moveType: 1,
        btn: ["提交", "关闭"],
        success: function (layero, index) {// 打开弹出层时，发生了什么？
            layui.use(['upload', 'form'], function () {// layui加载模块
                var form = layui.form;
                var upload = layui.upload;
                form.on('select(lang)', function (data) {// 获取lang
                    entity.lang = data.value;
                });

                var uploadInst = upload.render({//执行实例
                    elem: '#video'
                    , url: _ctx + "ugcManagement/uploadGVideo"
                    , accept: 'video'
                    , exts: 'mp4'
                    , data: entity
                    , auto: false
                    , bindAction: ".layui-layer-btn0"
                    , choose: function (obj) {// 选择文件之后，提交文件之前，你想干什么？
                        obj.preview(function (index, file, result) {
                            if (file.size > 0) {// 判断文件大小
                                flag = true;
                                layer.msg("Wait for me to disappear, forget I can be good");// 此段话，用来拖延用户点击提交按钮的时间，因为不知道covers是否返回
                            }
                            var url = URL.createObjectURL(file)
                            if (url != null) {// 显示video标签，用于预览
                                $('.video').css("display", "block");
                            }
                            $('#aa').attr("src", url);


                            var a = document.getElementById("aa");

                            var timesRun = 0;
                            var timer = setInterval(function () {// 定时器获取时长，，，，定时器只一次
                                timesRun += 1;
                                if (timesRun === 1) {
                                    clearInterval(timer);// 关闭定时器
                                }
                                entity.contentLen = parseInt(a.duration);// 获取视频时长，如果不使用定时器，获取时长可能是NAN，所以必须定时器
                                console.log(parseInt(a.duration));
                                // 视频缩略图-先获取video对象-用canvas画图，返回imageSrc，返回的是base64编码-然后解码，生成二进制blob文件，提交二进制文件到后台。如果这里不使用定时器，也获取不到图片。
                                var video = document.getElementById('aa');
                                var canvas = document.createElement('canvas');
                                var ctx = canvas.getContext('2d');
                                var imgHeight = video.videoHeight;
                                var imgWidth = video.videoWidth;
                                ctx.drawImage(video, 0, 0, imgWidth, imgHeight);
                                var imgSrc = canvas.toDataURL('image/png');
                                var binary = atob(imgSrc.split(',')[1]);
                                var array = [];
                                for (var i = 0; i < binary.length; i += 1) {
                                    array.push(binary.charCodeAt(i));
                                }
                                var blob = new Blob([new Uint8Array(array)], { type: 'image/png' });
                                var u = URL.createObjectURL(blob)
                                var formData = new FormData();
                                formData.append('file', blob);
                                $.ajax({
                                    url: _ctx + "pic/uploadImage",
                                    crossDomain: true,
                                    data: formData,
                                    dataType: 'json',
                                    type: 'POST',
                                    contentType: false,
                                    processData: false,
                                    success: function (data) {
                                        if (data != null) {
                                            entity.covers = JSON.stringify(data[0]);// 返回covers的JSON字符串
                                            coversflag = true; //这个没啥用、不想删、请善待。
                                        }
                                    }
                                });
                            }, 1000);
                        });
                    }
                    , done: function (res) {// 上传完成之后，干些事。
                        if (res.code == "1") {
                            layer.closeAll('loading');
                            layer.closeAll();
                            layer.msg("success");
                        } else {
                            layer.closeAll('loading');
                            layer.msg("error");
                        }

                    }
                });
            });
        },
        yes: function (layero, index) {// 提交按钮，做些什么？
            layui.use('form', function () {
                var form = layui.form;
                form.on('select(lang)', function (data) {
                    var lang = data.value;
                    console.log(data.value);
                });
            });
            var title = $("#title").val();
            entity.title = title;
            console.log(entity)
            if (flag) {
                layer.load();
            } else {
                layer.msg("文件不能为空！！！");
            }
        }
    });
}
var ugcUploadPage = {
    value: '<form class="layui-form">'
        + '<div class="layui-form-item">'
        + '<label class="layui-form-label">标题</label>'
        + '<div class="layui-input-block">'
        + ' <input type="text" id = "title" name="title" placeholder="请输入" autocomplete="off" class="layui-input">'
        + '</div>'
        + '</div>'
        + '<div class="layui-form-item">'
        + '<label class="layui-form-label">语言</label>'
        + '<div class="layui-input-block">'
        + '<select name="lang" lay-filter="lang">'
        + '<option value="0">英语</option>'
        + '<option value="1">印地语</option>'
        + '<option value="2">马拉地语</option>'
        + '<option value="3">泰米尔语</option>'
        + '</select>'
        + ''
        + '</div>'
        + '</div>'
        + '<div class="layui-form-item">'
        + '<label class="layui-form-label"></label>'
        + '<div class="layui-upload-drag" id="video">'
        + '<i class="layui-icon"></i>'
        + '<p>点击上传，或将文件拖拽到此处</p>'
        + '</div></br>'
        + '<span style="margin-left:2.9cm;color:red;">!仅可上传1个MP4格式视频文件</span>'
        + '</div>'
        + '<div class="layui-form-item video" style = "display:none">'
        + '<video src = "" id="aa" hight="140" width = "140" controls="controls"></video>'
        + '</div>'
        + '</form>'
}