<?php

namespace Nosh\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;

/**
 * The base command class is an abstract class with some helper methods.
 */
abstract class BaseCommand extends Command
{
    /**
     * Execute an external command and print anything using the provided
     * output class.
     * @param string $command
     * @param OutputInterface $output
     * @throws \Exception if the command fails.
     */
    protected function executeExternalCommand($command, OutputInterface $output)
    {
        $process = new Process($command);
        $process->run(function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        });
        if (!$process->isSuccessful()) {
            throw new \Exception("Command $command failed");
        }
    }

    /**
     * Get a twig environment.
     * @return \Twig_Environment
     *   A twig environment that points to the appropriate template directory.
     */
    protected function getTwig()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../templates');
        $twig = new \Twig_Environment($loader, array());
        return $twig;
    }

    protected function getThemes()
    {
        $themes = array();
        $finder = new Finder();
        $finder->name('theme.json');
        foreach (array(__DIR__ . '/../../themes', '~/.nosh') as $dir) {
            foreach ($finder->in(__DIR__ . '/../../themes') as $file) {
                $theme = json_decode($file->getContents(), true);
                $theme['dir'] = dirname($file->getRealPath());
            }
        }
        return $themes;
    }

    protected function getThemeDirs($themes, $theme, $dirs = array())
    {
        if (isset($theme['base'])) {
            if (!isset($themes[$theme['base']])) {
                throw new ThemeNotFoundException("The base theme {$theme['base']} was not found for theme $theme.");
            }
            $dirs[$theme['base']] = $themes[$theme['base']]['dir'];
            return $this->getThemeDirs($themes, $themes[$theme['base']], $dirs);
        }
        return $dirs;
    }

    protected function getTemplateEngine($type, $theme)
    {
        $themes = $this->getThemes();
        if (!isset($themes[$type][$theme])) {
            throw new ThemeNotFoundException("The theme $theme was not found");
        }
        return new TemplateEngine($this->getThemeDirs($themes[$type], $theme, array($theme => $themes[$type][$theme]['dir'])));
    }
}
