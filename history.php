<?php if (file_exists("history/".$ip)) { ?>
    <?php
    $path    = "history/".$ip;
    $files = array_diff(scandir($path), array('.', '..'));
    ?>
    <hr>
    <h5>Geçmiş</h5>
    <?php
        if(count($files) > 1){
    ?>
        <small>
            <a href="all-delete.php" onclick="return confirm('Tümünü silmek istediğinizden emin misiniz?')" style="color:red;">Tümünü Sil</a>
        </small>
    <?php
        }
    ?>
    <ul class="list-group">
    <?php
        foreach ($files as $file){
    ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="edit.php?file=<?php echo $file; ?>"><?php echo explode(".json",$file)[0]; ?></a>
                <span class="badge badge-danger badge-pill">
                    <a href="delete.php?file=<?php echo $file; ?>" onclick="return confirm('Silmek istediğinizden emin misiniz?')"
                       style="color: #fff;">
                        Sil
                    </a>
                </span>
            </li>
            <?php
        }
    ?>
    </ul>
<?php } ?>