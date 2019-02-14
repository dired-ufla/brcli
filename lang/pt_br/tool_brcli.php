<?php
/**
 * admin tool brcli
 * Backup & restore command line interface
 * @package admin
 * @subpackage tool
 * @author Paulo Júnior <pauloa.junior@ufla.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Interface de linha de comando para backup e restauração';
$string['unknowoption'] = 'Opção inválida: {$a}';
$string['helpoption'] = 
'Realiza o backup de todos os cursos de uma categoria.

Opções:
--categoryid=INTEGER        ID da categoria cujo backup será feito.
--destination=STRING        Caminho onde serão armazenados os arquivos de backup. Se não for informado
                            os arquivos serão armazenados no diretório admin/tool/brcli/bcks.
-h, --help                  Exibe a ajuda.

Exemplo:
    sudo -u www-data /usr/bin/php admin/tool/brcli/backup.php --categoryid=1 --destination=/moodle/backup/
';
$string['noadminaccount'] = 'Erro: Não há uma conta de administrador cadastrada!';
$string['directoryerror'] = 'Erro: O diretório de destino informado não existe ou não pode ser escrito!';
$string['nocategory'] = 'Erro: A categoria informada não existe!';
$string['performingbck'] = 'Iniciando backup do curso {$a}...';
$string['backupdone'] = 'Finalizado!';
