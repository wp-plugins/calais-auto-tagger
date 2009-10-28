<script type="text/javascript">
//<![CDATA[

        var suggestedTags = new Array();
        var currentTags = new Array();

        function calais_gettags() {
                document.getElementById('calais_suggestions').innerHTML = 'Getting suggestions...';
                jQuery.post('<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php', {text: calais_getcontent(), action: 'calais_gettags', cookie: document.cookie}, calais_showtags);

        }

        function calais_getcontent() {
                if (typeof tinyMCE != 'undefined' && !tinyMCE.selectedInstance.spellcheckerOn) {
                        if (typeof tinyMCE.triggerSave == 'function') {
                                tinyMCE.triggerSave();
                        } else {
                                tinyMCE.wpTriggerSave();
                        }
                }
                return document.getElementById('content').value;
        }

        function calais_showtags(tags) {
                suggestedTags = tags.split(', ');
                if (suggestedTags[0] == '')
                        suggestedTags = new Array();

                suggestions = document.getElementById('calais_suggestions');
                suggestions.innerHTML = '';

                for (i = 0; i < suggestedTags.length; i++) {
                        var el = document.createElement('div');
                        el.className = 'calais_tag';
                        el.id = 'suggestion_' + i;

                        var html = '<img src="<?php get_bloginfo('wpurl'); ?>/wp-content/plugins/calais-auto-tagger/images/add.png" onclick="calais_add_suggestion(\'suggestion_' + i + '\')" /> ' + suggestedTags[i];

                        el.innerHTML = html;
                        suggestions.appendChild(el);
                }

        }

        function calais_add_tag(tag) {
                found = false;
                for (j = 0; j < currentTags.length; j++) {
                        if (currentTags[j] == tag)
                                found = true;
                }
                if (found == false && tag != '') {
                        if (document.getElementById('calais_taglist').value != '') {
                                document.getElementById('calais_taglist').value = document.getElementById('calais_taglist').value + ', ' + tag;
                        } else {
                                document.getElementById('calais_taglist').value = tag;
                        }
                }
                calais_redisplay_tags();
        }

        function calais_add_suggestion(full_id) {
                id = full_id.replace('suggestion_', '');
                tag = suggestedTags[id];
                calais_add_tag(tag);
                document.getElementById(full_id).style.display = 'none';
        }

        function calais_add_manual() {
                newtags = document.getElementById('calais_manual').value.split(',');
                for (k = 0; k < newtags.length; k++) {
                        calais_add_tag(jQuery.trim(newtags[k]));
                }
                document.getElementById('calais_manual').value = '';
        }

        function calais_delete(full_id) {
                id = full_id.replace('ctag_', '');
                currentTags = document.getElementById('calais_taglist').value.split(', ');
                currentTags.splice(id, 1);
                var liststr = '';
                for (i = 0; i < currentTags.length; i++) {
                        liststr += currentTags[i];
                        if (i != (currentTags.length - 1)) {
                                liststr += ', ';
                        }
                }
                document.getElementById('calais_taglist').value = liststr;
                calais_redisplay_tags();
        }

        function calais_redisplay_tags() {

                calais_update_current_tags();
                existing = document.getElementById('calais_tag_box');
                existing.innerHTML = '';

                for (i = 0; i < currentTags.length; i++) {
                        var el = document.createElement('div');
                        el.className = 'calais_tag';
                        el.id = 'ctag_' + i;

                        var html = '<img src="<?php get_bloginfo('wpurl'); ?>/wp-content/plugins/calais-auto-tagger/images/delete.png" onclick="calais_delete(\'ctag_' + i + '\')" /> ' + currentTags[i];

                        el.innerHTML = html;
                        existing.appendChild(el);
                }
        }

        function calais_update_current_tags() {
                temp = document.getElementById('calais_taglist').value.split(', ');
                if (temp.length > 1) {
                        currentTags = temp;
                } else {
                        if (temp[0] != '') currentTags = temp;
                }
        }

        jQuery(document).ready(function() { document.getElementById('tagsdiv-post_tag').style.display = 'none'; });

//]]>
</script>
