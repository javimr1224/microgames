import React, { useEffect, useRef } from "react";
import Phaser from "phaser";
import { Button } from './ui/button';
import { ArrowLeft } from 'lucide-react';

class SkyboundScene extends Phaser.Scene {
  player!: Phaser.Physics.Arcade.Sprite;
  platforms!: Phaser.Physics.Arcade.StaticGroup;
  coins!: Phaser.Physics.Arcade.Group;
  cursors!: Phaser.Types.Input.Keyboard.CursorKeys;
  score = 0;
  scoreText!: Phaser.GameObjects.Text;

  highestPlatformY = 0;
  maxHeight = 0;

  constructor() {
    super("SkyboundScene");
  }

  preload() {
    this.load.image(
      "player",
      "https://cdn.jsdelivr.net/gh/samme/phaser-examples-assets@v2.0.0/assets/sprites/phaser-dude.png"
    );
    this.load.image(
      "island",
      "https://cdn.jsdelivr.net/gh/samme/phaser-examples-assets@v2.0.0/assets/sprites/platform.png"
    );
    this.load.image(
      "coin",
      "https://cdn.jsdelivr.net/gh/samme/phaser-examples-assets@v2.0.0/assets/sprites/coin.png"
    );
  }

  create() {
    this.cameras.main.setBackgroundColor("#87ceeb");

    this.physics.world.setBounds(
      0,
      -100000,
      window.innerWidth,
      100000
    );

    this.platforms = this.physics.add.staticGroup();
    this.coins = this.physics.add.group();

    this.platforms
      .create(400, 550, "island")
      .setScale(2)
      .refreshBody();

    let minY = 550;

    for (let i = 1; i <= 10; i++) {
      const x = Phaser.Math.Between(100, 700);
      const y = 550 - i * 120;
      minY = Math.min(minY, y);
      this.platforms.create(x, y, "island").refreshBody();
    }

    this.highestPlatformY = minY;

    this.player = this.physics.add.sprite(400, 400, "player");
    this.player.setBounce(0.1);
    this.player.setCollideWorldBounds(false);

    this.maxHeight = this.player.y;

    this.physics.add.collider(this.player, this.platforms);

    this.physics.add.overlap(
      this.player,
      this.coins,
      (_player, coin) => this.collectCoin(coin),
      undefined,
      this
    );

    this.cursors = this.input.keyboard.createCursorKeys();

    this.scoreText = this.add.text(16, 16, "Monedas: 0", {
      fontSize: "20px",
      color: "#000",
    });
    this.scoreText.setScrollFactor(0);

    this.cameras.main.startFollow(this.player);

    this.spawnCoin();
  }

  spawnCoin() {
    if (this.coins.countActive(true) > 0) return;

    const platforms = this.platforms.getChildren() as Phaser.Physics.Arcade.Image[];
    const platformsAbove = platforms.filter((platform) => platform.y < this.player.y);

    const platform = Phaser.Math.RND.pick(
      platformsAbove.length ? platformsAbove : platforms
    );

    const coin = this.coins.create(
      platform.x,
      platform.y - 40,
      "coin"
    );

    coin.body.setAllowGravity(false);
    coin.body.setImmovable(true);
  }

  collectCoin(coin: Phaser.GameObjects.GameObject) {
    coin.destroy();
    this.score++;
    this.scoreText.setText(`Monedas: ${this.score}`);
    this.spawnCoin();
  }

  generateMorePlatforms() {
    let minY = this.highestPlatformY;

    for (let i = 0; i < 10; i++) {
      const x = Phaser.Math.Between(100, 700);
      const y = this.highestPlatformY - (i + 1) * 120;
      minY = Math.min(minY, y);
      this.platforms.create(x, y, "island").refreshBody();
    }

    this.highestPlatformY = minY;
  }

  update() {
    if (!this.player.body) return;

    if (this.cursors.left?.isDown) {
      this.player.setVelocityX(-200);
    } else if (this.cursors.right?.isDown) {
      this.player.setVelocityX(200);
    } else {
      this.player.setVelocityX(0);
    }

    if (this.cursors.up?.isDown && this.player.body.blocked.down) {
      this.player.setVelocityY(-450);
    }

    this.maxHeight = Math.min(this.maxHeight, this.player.y);

    if (this.player.y < this.highestPlatformY + 300) {
      this.generateMorePlatforms();
    }

    if (this.player.y > this.maxHeight + 700) {
      this.scene.restart();
    }
  }
}

interface SkyboundGameProps {
  onBack: () => void;
}

export default function SkyboundGame({ onBack }: SkyboundGameProps) {
  const gameRef = useRef<Phaser.Game | null>(null);

  useEffect(() => {
    if (gameRef.current) return;

    gameRef.current = new Phaser.Game({
      type: Phaser.AUTO,
      width: window.innerWidth,
      height: window.innerHeight,
      physics: {
        default: "arcade",
        arcade: {
          gravity: { x: 0, y: 500 },
          debug: false,
        },
      },
      scene: SkyboundScene,
      parent: "skybound-container",
    });

    const resize = () => {
      gameRef.current?.resize(window.innerWidth, window.innerHeight);
    };

    window.addEventListener("resize", resize);

    return () => {
      window.removeEventListener("resize", resize);
      gameRef.current?.destroy(true);
      gameRef.current = null;
    };
  }, []);

  return (
    <div className="relative w-full h-full">
      <Button onClick={onBack} variant="outline" size="sm" className="absolute mt-12 bg-red-600 hover:bg-red-700 border-red-500 text-white">
        <ArrowLeft className="w-4 h-4 mr-2" /> VOLVER
      </Button>
      <div id="skybound-container" className="w-full h-full" />
    </div>
  );
}
