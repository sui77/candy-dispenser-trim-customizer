FROM ubuntu:lunar

# props to https://github.com/ikester/blender-docker

RUN apt-get update && \
	apt-get install -y \
		curl \
		libfreetype6 \
		libglu1-mesa \
		libxi6 \
		libxrender1 \
        libxxf86vm-dev \
        libxfixes-dev \
        libxkbcommon-x11-0 \
        libsm-dev \
        libgl-dev \
		xz-utils && \
	apt-get -y autoremove && \
	rm -rf /var/lib/apt/lists/*

ENV BLENDER_MAJOR 3.5
ENV BLENDER_VERSION 3.5.1
#ENV BLENDER_URL https://download.blender.org/release/Blender${BLENDER_MAJOR}/blender-${BLENDER_VERSION}-linux-x64.tar.xz
ENV BLENDER_URL https://sui.li/blender-${BLENDER_VERSION}-linux-x64.tar.xz

RUN curl -L ${BLENDER_URL} | tar -xJ -C /usr/local/ && \
	mv /usr/local/blender-${BLENDER_VERSION}-linux-x64 /usr/local/blender

VOLUME /media
ENTRYPOINT ["/usr/local/blender/blender", "-b"]