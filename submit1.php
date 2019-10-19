<?php
/**
 * View/download problem texts and sample testcases
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */

require('init.php');

$title = 'Submit';
require(LIBWWWDIR . '/header.php');

echo "<h1>Submit</h1>\n\n";

$fdata = calcFreezeData($cdata);
if (!$fdata['started'] && !checkrole('jury')) {
    echo '<div class="container submitform"><div class="alert alert-danger" role="alert">Contest has not yet started - cannot submit.</div></div>';
    require(LIBWWWDIR . '/footer.php');
    exit;
}

$langdata = $DB->q('KEYTABLE SELECT langid AS ARRAYKEY, name, extensions, require_entry_point, entry_point_description
                    FROM language WHERE allow_submit = 1');

$probdata = $DB->q('TABLE SELECT probid, shortname, name FROM problem
                    INNER JOIN contestproblem USING (probid)
                    WHERE cid = %i AND allow_submit = 1
                    ORDER BY shortname', $cid);

print "<script>";
putgetMainExtension($langdata);
print "</script>";

$maxfiles = dbconfig_get('sourcefiles_limit', 100);

$probs = array();
$probs[''] = 'Select a problem';
foreach ($probdata as $probinfo) {
    $probs[$probinfo['probid']] = $probinfo['shortname'] . ' - ' . $probinfo['name'];
}

$langs = array();
$langs[''] = 'Select a language';
foreach ($langdata as $langid => $langdata) {
    $langs[$langid] = $langdata['name'];
}

?>
    <div class="container submitform">
        <form action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return checkUploadForm();">
            <div class="form-group">
                <label for="probid">Problem:</label>
                <select class="custom-select" name="probid" id="probid" required>
                    <?php
                    foreach ($probs as $probid => $probname) {
                        print '      <option value="' . specialchars($probid) . '">' . specialchars($probname) . "</option>\n";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="langid">Language:</label>
                <select class="custom-select" name="langid" id="langid" required>
                    <?php
                    foreach ($langs as $langid => $langname) {
                        print '      <option value="' . specialchars($langid) . '">' . specialchars($langname) . "</option>\n";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="maincode">Source code:</label>
                <textarea class="form-control" id="codearea" rows="15" autocorrect="off" autocapitalize="off"
                          spellcheck="false" style="font-size: 14px;"></textarea>
            </div>
        </form>
        <button id="submitCodeBtn" class="btn btn-primary" onclick="newPostMethod()">Submit</button>
        <br><br>
        <a href="submit.php">Submit file -></a>
        <script type="text/javascript">initFileUploads(<?=$maxfiles?>);</script>
    </div>
    <script type="text/javascript">
        document.getElementById("submitCodeBtn").onclick = function () {
            var code = document.getElementById('codearea').value;
            var probid = document.getElementById('probid').value;
            var langid = document.getElementById('langid').value;
            if (probid.length === 0) {
                alert("Please select a problem.");
                return;
            }
            if (langid.length === 0) {
                alert("Please select a language.");
                return;
            }
            if (code.length === 0) {
                alert("Source code can't be empty.");
                return;
            }
            var boundary = "---------------------------WebkitBoundary" + Math.floor(Math.random() * 1e9 + 1);
            var body = '--' + boundary + '\r\n'
                + 'Content-Disposition: form-data; name="code[]";filename="codeRaw."'+langid+'\r\n'
                + 'Content-type: application/octet-stream\r\n\r\n'
                + code + '\r\n'
                + '--' + boundary + '\r\n'
                + 'Content-Disposition: form-data; name="probid"\r\n\r\n' +
                probid + '\r\n'
                + '--' + boundary + '\r\n'
                + 'Content-Disposition: form-data; name="langid"\r\n\r\n' +
                langid + '\r\n'
                + '--' + boundary + '\r\n'
                + 'Content-Disposition: form-data; name="entry_point"\r\n\r\n' +
                'codeRaw' + '\r\n'
                + '--' + boundary + '\r\n'
                + 'Content-Disposition: form-data; name="submit"\r\n\r\n' +
                'Submit' + '\r\n'
                + '--' + boundary + '--\r\n';
            console.log(body);
            $.ajax({
                contentType: "multipart/form-data; boundary=" + boundary,
                data: body,
                type: "POST",
                url: "/team/upload.php",
                success: function (data, status) {
                    window.location.href = "/team/index.php";
                }
            });
        }
    </script>
<?php
require(LIBWWWDIR . '/footer.php');
