<?php
namespace develnext\project\type;

use develnext\project\dependency\MavenProjectDependency;
use develnext\project\Project;
use develnext\project\ProjectFile;
use develnext\project\ProjectType;
use php\io\FileStream;
use php\lib\str;

abstract class JVMProjectType extends ProjectType {

    protected static $specialPaths = [
        '/src' => ['Sources', 'images/icons/filetype/sources.png'],
        '/resources' => ['Resources', 'images/icons/filetype/resources.png'],
        '/resources/forms' => [null, 'images/icons/filetype/forms.png'],
        '/resources/images' => [null, 'images/icons/filetype/images.png'],
    ];

    function getDefaultDependencies() {
        return [
            new MavenProjectDependency('org.develnext', 'jphp-core', '0.4-SNAPSHOT')
        ];
    }

    function onCreateProject(Project $project) {
        $this->updatePom($project);

        $project->getFile('src/')->mkdirs();
        $project->getFile('resources/')->mkdirs();

        $project->getFile('resources/JPHP-INF')->mkdirs();

        $bootstrap = new FileStream($project->getPath('src/bootstrap.php'), 'w+');
        $bootstrap->write('<?php ');
        $bootstrap->close();

        $conf = new FileStream($project->getPath('resources/JPHP-INF/launcher.conf'), 'w+');
        $conf->write("env.debug = 0\n\n");
        $conf->write("env.extensions = spl, org.develnext.jphp.swing.SwingExtension\n\n");
        $conf->write("bootstrap.file = bootstrap.php\n\n");
        $conf->close();
    }

    function onUpdateProject(Project $project) {
        $this->updatePom($project);
    }

    function onRenderFileInTree(ProjectFile $file) {
        $relPath = $file->getRelPath();
        $info = self::$specialPaths[$relPath];

        if ($info) {
            return $file->duplicate($info[0], $info[1]);
        }

        if (str::startsWith($relPath, '/resources/JPHP-INF'))
            return null;

        return $file;
    }


    protected function updatePom(Project $project) {
        $out = new FileStream($project->getDirectory()->getPath() . '/pom.xml', 'w+');

        $version = "1.0";
        $name = $project->getName();

        $out->write(<<<"DOC"
<project>
  <modelVersion>4.0.0</modelVersion>
  <groupId>org.develnext.project</groupId>
  <artifactId>$name</artifactId>
  <version>$version</version>

  <repositories>
    <repository>
      <id>DevelNext Repo</id>
      <url>http://maven.develnext.org/repository/snapshots/</url>
    </repository>
    <repository>
      <id>central</id>
      <name>Maven Repository Switchboard</name>
      <layout>default</layout>
      <url>http://repo1.maven.org/maven2</url>
      <snapshots>
        <enabled>false</enabled>
      </snapshots>
    </repository>
  </repositories>
</project>


DOC
);

        $out->write("<dependencies>\n");

        foreach($project->getDependencies() as $dep) {
            if ($dep instanceof MavenProjectDependency) {
                $out->write("\t<dependency>\n");
                $out->write("\t\t<groupId>" . $dep->getGroupId()  . "</groupId>\n");
                $out->write("\t\t<artifactId>" . $dep->getArtifactId() . "</artifactId>\n");
                $out->write("\t\t<version>" . $dep->getVersion() . "</version>\n" );
                $out->write("\t</dependency>\n");
            }
        }

        $out->write("</dependencies>\n");
        $out->close();
    }
}