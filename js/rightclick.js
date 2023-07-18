$(document).ready(function () {
    var tvsnactions = {
        init: function () {
            OCA.Files.fileActions.registerAction({
                name: 'tvsn-scan',
                displayName: 'Scan With TV Show Namer',
                mime: 'dir',
                permissions: OC.PERMISSION_UPDATE,
                type: OCA.Files.FileActions.TYPE_DROPDOWN,
                iconClass: 'icon-tvsn',
                actionHandler: function (folder, context) {
                    window.location = OC.generateUrl(
                        'apps/tvshownamer/#scan=/{dir}',
                        { dir: folder });
                }
            });

        },
    }
    tvsnactions.init();
});