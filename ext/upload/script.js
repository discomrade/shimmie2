function fileSize(size) {
    var i = Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ['B', 'kB', 'MB', 'GB', 'TB'][i];
}
function updateTracker() {
    var size = 0;
    var upbtn = document.getElementById("uploadbutton");
    var tracker = document.getElementById("upload_size_tracker");
    var lockbtn = false;

    // check that each individual file is less than the max file size
    document.querySelectorAll("#large_upload_form input[type='file']").forEach((input) => {
        var cancelbtn = document.getElementById("cancel"+input.id);
        var toobig = false;
        if (input.files.length) {
            if(cancelbtn) cancelbtn.style.visibility = 'visible';
            for (var i = 0; i < input.files.length; i++) {
                size += input.files[i].size + 1024; // extra buffer for metadata
                if (window.shm_max_size > 0 && input.files[i].size > window.shm_max_size) {
                    toobig = true;
                }
            }
            if (toobig) {
                lockbtn = true;
                input.style = 'color:red';
            } else {
                input.style = '';
            }
        } else {
            if(cancelbtn) cancelbtn.style.visibility = 'hidden';
            input.style = '';
        }
    });

    // check that the total is less than the max total size
    if (size) {
        tracker.innerText = fileSize(size);
        if (window.shm_max_total_size > 0 && size > window.shm_max_total_size) {
            lockbtn = true;
            tracker.style = 'color:red';
        } else {
            tracker.style = '';
        }
    } else {
        tracker.innerText = '0MB';
    }
    upbtn.disabled = lockbtn;

    // if ratings are enabled, make a custom rating required (Unrated is invalid)
    document.querySelectorAll("#large_upload_form input[type='file']").forEach((input) => {
        if (input.files.length) {
            id = input.id.replace("data","");
            rating = document.querySelector(`[name=rating${id}]`);
            // change Unrated value to empty, this triggers the 'required' attr of select element
            unrated = rating.querySelector("[value='?']");
            if (unrated) {
                unrated.value = "";
            }
        }
    });
}
document.addEventListener('DOMContentLoaded', () => {
    if(document.getElementById("upload_size_tracker")) {
        document.querySelectorAll("#large_upload_form input[type='file']").forEach((el) => {
            el.addEventListener('change', updateTracker);
        });
        updateTracker();    
    }
});
