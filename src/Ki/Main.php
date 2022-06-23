<?php

namespace Ki;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory; 
use pocketmine\player\Player;
use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\Server;
use pocketmine\plugin\PluginBase; 
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;

use onebone\coinapi\CoinAPI;
use onebone\economyapi\EconomyAPI;


class Main extends PluginBase implements Listener {
	
  private static PluginLang $lang;
  
  public function onEnable():void{
    $this->getLogger()->info("Plugin Code By PmmdSt");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    $this->coin = $this->getServer()->getPluginManager()->getPlugin("CoinAPI");
  }
  
  public function onDisable():void{
    $this->getLogger()->info("Plugin Da Bi Tat");
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        if($cmd->getName() == "doixu"){
            $this->GiaoDien($sender);
            } 
       return true; 
       } 
  
  public function Giaodien(Player $sender){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function (Player $sender, ?int $data = null){
        $result = $data;
        if($result === null){
          return true;
        }
      switch ($result) {
        case 0:
          $this->DoiCoin($sender);
          break;
        case 1:
          $this->DoiDa($sender);
          break;
        case 2:
          $this->DoiMoney($sender);
          break;
      }
      
    });
    $money = $this->money->myMoney($sender);
    $coin = $this->coin->myCoin($sender);
    $form->setTitle("§lGIAO DIỆN Đổi Đồ");
    $form->setContent("§7[§e➸§7]§7 Tiền của bạn: §e" . $money.", §7coin của bạn:§e ".$coin);
    $form->addButton("§l§c• §9Đổi Coin §c•\n§r§8Nhấn để xem",1,"https://cdn-icons-png.flaticon.com/512/1490/1490853.png");
    $form->addButton("§l§c• §9Đổi Đá Donate §c•\n§r§8Nhấn để xem",1,"https://cdn-icons-png.flaticon.com/512/4405/4405457.png");
    $form->addButton("§l§c• §9Đổi Money §c•\n§r§8Nhấn để xem",1,"https://cdn-icons-png.flaticon.com/512/639/639365.png");
    $form->sendToPlayer($sender);
    return $form;
  }
  
  public function DoiCoin(Player $sender){
       $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
       $form = $api->createCustomForm(function(Player $sender, array $data = null){
                  if($data == null) return false;
                  if($data[1]== null) return false;
                 if(!is_numeric($data[1])){
       		  $player->sendMessage("§l§f§•[§c+§f]§r Vui lòng Nhập Số");
       		  return false;
       	  }
                $money = EconomyAPI::getInstance()->myMoney($sender);
                if($money >= $data[1]*5000000){
                  $this->money->reduceMoney($sender, $data[1]*5000000);
                  $this->coin->addCoin($sender, $data[1]);
                  $sender->sendMessage("§l§f§•[§c+§f]§r Đã đổi thành công §e" . $data[1] . " §aCoint");
                }else{
                  $sender->sendMessage("§l§f§•[§c+§f]§r Bạn không có đủ money");
                }
              });
              $form->setTitle("§l§1• §cĐổi Xu Thành Coint §1•");
              $form->addLabel("§l§eHãy Nhập Số Coint Muốn Đổi");
              $form->addInput("§l§f§•[§c+§f]§r 5.000.000 money = 1 Coint", "0");
              $form->sendToPlayer($sender);
              return $form;
              }
              
  public function DoiDa(Player $sender){ 
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createCustomForm(function(Player $sender, array $data = null){
           if($data == null) return false;
           if($data[1]== null) return false;
          if(!is_numeric($data[1])){
		  $player->sendMessage("§l§f§•[§c+§f]§r Vui lòng Nhập Số xu bạn muốn mua");
		  return false;
	  }
	      
           $coin = $this->coin->myCoin($sender);
         if($coin >= $data[1]*10){
           $this->coin->reduceCoin($sender->getName(), $data[1]*10);
           $inv = $sender->getInventory();
           $item = ItemFactory::getInstance()->get(318, 0, $data[1]);
           $item->getNamedTag()->setString("donate", "donate");
           $item->setCustomName("§l§6Xu §6Donate");
           $item->setLore(["\n§r§l§r- Vật phẩm để đổi đồ trong §awarp donate\n §l§cLưu ý:§r không trồng xuốn đất sẽ bị mất \n§r(click lên không để đổi về xu)"]);
           
           $inv->addItem($item);
           $sender->sendMessage("§a Bạn đã nhận được ".$data[1]." §rxu donate");
           
         }
    });
    $form->setTitle("§l§1• §aMua §eXu Donate §1•");
    $form->addLabel("§l§eHãy Nhập Số Xu Bạn muốn mua");
    $form->addInput("§l§f§•[§c+§f]§r 10 coin = 1 Xu", "0");
    $form->sendToPlayer($sender);
    return $form;
       }
  public function onuse(PlayerInteractEvent $event){
    $player = $event->getPlayer();
    $item = $event->getItem();
    if (!$item->getNamedTag()->getTag("donate")) {
            return true;
        }
    $item->setCount($item->getCount() - 1);
    $player->getInventory()->setItemInHand($item);
    $this->coin->addCoin($player, 10);
    $player->sendMessage("§aBạn vừa dùng đá trade và nhận được §e10 Coin");
    }
  public function DoiMoney(Player $sender){
       $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
       $form = $api->createCustomForm(function(Player $sender, array $data = null){
                  if($data == null) return false;
                  if($data[1]== null) return false;
                 if(!is_numeric($data[1])){
       		  $player->sendMessage("§l§f§•[§c+§f]§r Vui lòng Nhập Số");
       		  return false;
       	  }
                $coin = CoinAPI::getInstance()->myCoin($sender);
                if($coin >= $data[1]*1){
                  $this->coin->reduceCoin($sender, $data[1]*1);
                  $this->money->addMoney($sender, $data[1]*4500000);
                  $sender->sendMessage("§l§f§•[§c+§f]§r Đã đổi thành công §6" . $data[1]*4500000 . "§a Money");
                }else{
                  $sender->sendMessage("§l§f§•[§c+§f]§r Bạn không có đủ Coin");
                }
              });
              $form->setTitle("§l§1• §cĐổi Coin Thành Money §1•");
              $form->addLabel("§l§eHãy Nhập Số Coint Muốn Đổi Ra money");
              $form->addInput("§l§f§•[§c+§f]§r 1 Coin = 4.500.000", "0");
              $form->sendToPlayer($sender);
              return $form;
              }
  }
 