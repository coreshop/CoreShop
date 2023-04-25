<?php if(!empty($params['version']) && !empty($params['version_map'])) { ?>

    <?php $versionMap = $params['version_map']; ?>
    <?php if($versionMap['hasMultipleVersions']) { ?>

        <span class="version-switcher">
            Version:
            <select onchange="document.location.href=this.value">

                <?php
                    $maintenanceStates = array_keys($versionMap['versions']);
                    ksort($maintenanceStates);
                ?>

                <?php foreach($maintenanceStates as $state) { ?>

                    <optgroup label="<?= $state?>">

                        <?php
                            $versions = array_keys($versionMap['versions'][$state]);

                            //sort based on version name
                            $versionNames = [];
                            foreach($versions as $version) {
                                $versionNames[$version] = $versionMap['versions'][$state][$version]['name'];
                            }
                            arsort($versionNames, SORT_NATURAL);
                            $versions = array_keys($versionNames);
                        ?>

                        <?php foreach($versions as $version) { ?>
                            <?php if(!empty($versionMap['versions'][$state][$version]['paths'][$page['relative_path']])) { ?>
                                <option
                                        value="<?= $params['version_switch_path_prefix']?>/<?= $version ?>/<?= $page['request'] ?>"
                                        <?= $version == $params['version'] ? 'SELECTED' : '' ?>
                                >
                            <?= $versionMap['versions'][$state][$version]['name'] ?>
                        </option>
                            <?php } ?>
                        <?php } ?>

                    </optgroup>

                <?php } ?>

            </select>
        </span>


    <?php } ?>

<?php } ?>