<?php echo $form; ?>
<?php echo isset($repo->id) ? $repo->id : 0; ?>

<script language="javascript">

    var repo_id = <?php echo isset($repo->id) ? $repo->id : 0; ?>;

    $(document).ready(function() {
        bindTypeChangeEvent();
    });

    function bindTypeChangeEvent() {
        $("#owner").on("change", function(event) {
            getGithubRepo();
        });
        $("#repo").on("change", function(event) {
            getGithubRepo();
        });
    }

    function getGithubRepo() {
        
        var github_owner = $("#owner").val();
        var github_repo = $("#repo").val();
     
        // If we have both a owner and a repo then go and check if it exists
        // in GitHub
        if (github_owner && github_repo) {
            $('#repo').parent().children('.alert-box').remove();
            var path = "/github/ajaxCheckGithubRepo/" + github_owner + "/" + github_repo + "/" + repo_id;
    
            $.getJSON(path,function(result) {
                if (result.hasOwnProperty('errmsg'))
                    $('#repo').parent().append('<div data-alert class="alert-box warning">'+result.errmsg+'</div>');
                else
                    $("#url").val(result.html_url);
            });
        }

        return;    
    }
    
    function selectAutocompleteCallback(event, ui) {
    	if (event.target.id == "acp_taskgroup_id") {
            getTaskGroupData(ui.item.id);
    	}
    }
    
    function getTaskGroupData(taskgroup_id) {
        console.log(taskgroup_id);
        $.getJSON("/task/taskAjaxSelectbyTaskGroup/" + taskgroup_id + "/<?php echo !empty($task->id) ? $task->id : null; ?>",
            function(result) {
                $('#task_type').parent().html(result[0]);
            }
        );
    }
    
        
</script>