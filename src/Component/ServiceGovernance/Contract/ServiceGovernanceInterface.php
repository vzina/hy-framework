<?php
/**
 * ServiceGovernanceInterface.php
 * PHP version 7
 *
 * @category hyperf-client
 * @author   Weijian.Ye <yeweijian@3k.com>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/vzina
 * @date     2020-08-27
 */
declare(strict_types=1);

namespace EyPhp\Framework\Component\ServiceGovernance\Contract;


interface ServiceGovernanceInterface
{
    public function register(string $name, string $address, int $port, string $protocol);

    public function deregister(string $name, string $address, int $port, string $protocol);
}