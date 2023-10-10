FROM alpine:3.7

# add user
RUN adduser kShell -h / -s /bin/sh -D -u 1337 -h /home/kShell/ && passwd -u kShell

# copy file
ADD kShell.py /
ADD readflag /
ADD flag /
ADD ssh.pub /
ADD start.sh /


# cmd
RUN apk update
RUN apk add htop procps libcap python3 openssh openrc

# permission
RUN chmod 400 /flag
RUN chmod +x /readflag
RUN chmod +s /readflag
RUN chmod 774 /kShell.py
RUN chmod +x /start.sh

# setup
ADD sshd_config /etc/ssh/sshd_config
RUN ssh-keygen -A
RUN mkdir /home/kShell/.ssh && mv /ssh.pub /home/kShell/.ssh/authorized_keys && chown -R kShell:kShell /home/kShell/.ssh

WORKDIR /home/kShell
CMD ["/start.sh"]