# -*- mode: ruby -*-
# vi: set ft=ruby :
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "puppetlabs/centos-7.2-64-nocm"

  # Automatically add this vm to /etc/hosts. See: https://github.com/smdahlen/vagrant-hostmanager
  #  if Vagrant.has_plugin?("vagrant-hostmanager")
  #    config.hostmanager.enabled = true
  #    config.hostmanager.manage_host = true
  #    config.hostmanager.ignore_private_ip = false
  #    config.hostmanager.include_offline = true
  #
  #      # Handle dynamic addresses (DHCP)
  #    config.hostmanager.ip_resolver = proc do |vm, resolving_vm|
  #      if hostname = (vm.ssh_info && vm.ssh_info[:host])
  #        `vagrant ssh -c "hostname -I"`.split()[1]
  #      end
  #    end
  #  end
  config.ssh.username = "vagrant"
  config.vm.provider :virtualbox do |v|
    v.name = "armpitvm"
    v.memory = 512
    v.cpus = 1
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--ioapic", "on"]
  end

  config.vm.hostname = "armpit.vagrant.dev"
  config.vm.network :private_network, type: "dhcp"
  # for grunt server and live-reload
  # config.vm.network :forwarded_port, guest: 9000, host: 9000
  # config.vm.network :forwarded_port, guest: 35729, host: 35729


  # Set the name of the VM. See: http://stackoverflow.com/a/17864388/100134
  config.vm.define :armpitvm do |armpitvm|
  end

  #Disable the default share
  #  config.vm.synced_folder ".", "/vagrant", disabled: true
  #
  #  #Share ~/vagrant_shares/arm on the host to /opt/site on the guest
  #  #See: https://github.com/gael-ian/vagrant-bindfs
  #  if Vagrant.has_plugin?("vagrant-bindfs")
  #    config.vm.synced_folder "~/vagrant_shares/armpit", "/opt_share_nfs", create: true, :nfs => { :mount_options => ['rw', 'vers=3', 'tcp', 'nolock'] }
  #    config.bindfs.bind_folder "/opt_share_nfs", "/opt/site", :owner => "1000", :group => "1000", :'create-as-user' => true, :perms => "u=rwx:g=rwx:o=rwx", :'create-with-perms' => "u=rwx:g=rwx:o=rwx", :'chown-ignore' => true, :'chgrp-ignore' => true, :'chmod-ignore' => true
  #  end

  config.vm.provision :ansible do |ansible|
    ansible.playbook = "build-playbook.yml"
    ansible.groups = {
      "armpit" => ["armpitvm"]
    }
    ansible.sudo = true
    # ansible.extra_vars = {
    #      vagrant_vm: true,
    #      force_remote_user: true,
    #      remote_user: "vagrant",
    #      ansible_ssh_user: "vagrant",
    #      arm_web_fqdn: "armpit.vagrant.dev",
    #      web_server_group: "vagrant",
    #      web_server_user: "vagrant"
    # }
  end
  
  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  # config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
  #   vb.memory = "1024"
  # end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  # config.vm.provision "shell", inline: <<-SHELL
  #   sudo apt-get update
  #   sudo apt-get install -y apache2
  # SHELL
end
