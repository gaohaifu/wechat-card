if (Config.editpage.app_debug == true) {
    require.config({
        paths: {
            ace: ['../addons/editpage/js/src-min-noconflict/ace'],
            tools: ['../addons/editpage/js/src-min-noconflict/ext-language_tools']
        },
        shim: {
            'tools': {
                deps: [
                    'ace'
                ]
            }
        }
    });
    if (Config.editpage.module == 'admin' && ['editpage', 'index'].indexOf(Config.editpage.controller.toLowerCase()) == -1) {
        //浮动按钮
        var _html = '<div id="editpage" style="position: fixed;right: 0;top: 20%;z-index: 999;flex-flow: column;right: 5px;">' +
            '<a style="display: flex;margin-bottom: 2px;" href="javascript:;" data-type="c" class="btn btn-primary" title="控制器">C</a>' +
            '<a style="display: flex;margin-bottom: 2px;" href="javascript:;" data-type="m" class="btn btn-info" title="模型">M</a>' +
            '<a style="display: flex;margin-bottom: 2px;" href="javascript:;" data-type="v" class="btn btn-success" title="视图">V</a>' +
            '<a style="display: flex;margin-bottom: 2px;" href="javascript:;" data-type="j" class="btn btn-danger" title="JS">J</a>' +
            '<a style="display: flex;margin-bottom: 2px;" href="javascript:;" data-type="l" class="btn btn-warning" title="Lang">L</a>' +
            '<a style="display: flex;margin-bottom: 2px;" href="javascript:;" data-type="command" class="btn btn-primary" title="命令行">&lt;</a>' +
            '</div>';
        $("body").append(_html);
        //触发弹窗
        $('#editpage').find('a').click(function () {
            var title = $(this).attr('title');
            var type = $(this).attr('data-type');
            if(type == 'command'){
                var url = Config.editpage.command;
            }else{
                var url = Config.editpage.index + '?module=' + Config.editpage.module + '&c=' + Config.editpage.controller + '&a=' + Config.editpage.action + '&type=' + type;
            }
            parent.Fast.api.open(url, title, {area: ["80%", "80%"]});
        });
    }
}